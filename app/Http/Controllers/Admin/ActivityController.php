<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Models\Activity;
use App\Models\Schedule;
use App\Support\Destinations;
use App\Support\RichText;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public const CATEGORIES = ['Photography', 'Cultural Show', 'Wildlife & Nature', 'Water Sports', 'Adventure', 'Culture & Heritage'];

    /** Sort pill options (Figma 1:10022); the key is the query value. */
    public const SORTS = [
        'most_booked' => 'Most Booked',
        'top_rated' => 'Top Rated',
        'newest' => 'Newest',
        'price_low' => 'Price: Low to High',
        'price_high' => 'Price: High to Low',
    ];

    /** Activity listing — Figma node 1:9970. */
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->value();
        $sort = array_key_exists($sort, self::SORTS) ? $sort : 'most_booked';

        $activities = Activity::query()
            // "Total sold" is counted from confirmed bookings rather than stored on the row,
            // so it always matches what the Booking Report shows.
            ->withSum(
                ['bookings as sold_pax' => fn ($q) => $q->where('status', BookingStatus::Confirmed)],
                DB::raw('adults + children'),
            )
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->string('q').'%')
                ->orWhere('location', 'like', '%'.$request->string('q').'%')
                ->orWhere('place_label', 'like', '%'.$request->string('q').'%')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')->value()))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->value()))
            ->tap(fn ($q) => match ($sort) {
                'top_rated' => $q->orderByDesc('rating'),
                'newest' => $q->latest(),
                'price_low' => $q->orderBy('price_adult'),
                'price_high' => $q->orderByDesc('price_adult'),
                default => $q->orderByDesc('sold_pax'),
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.activities', [
            'activities' => $activities,
            'filters' => $request->only(['q', 'category', 'status']) + ['sort' => $sort],
            'categories' => self::CATEGORIES,
            'sorts' => self::SORTS,
        ]);
    }

    /** Add New Activity — Figma node 1:8502. */
    public function create(): View
    {
        return $this->form(new Activity(['status' => ListingStatus::Active, 'opens_at' => '08:00', 'closes_at' => '18:00']));
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = Activity::query()->create($this->payload($request));

        return redirect()->route('admin.activities')->with('flash', "{$activity->name} created.");
    }

    public function edit(Activity $activity): View
    {
        return $this->form($activity);
    }

    public function update(StoreActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($this->payload($request, $activity));

        return redirect()->route('admin.activities')->with('flash', "{$activity->name} updated.");
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('admin.activities')->with('flash', "{$activity->name} removed.");
    }

    /** Delete everything ticked in the listing. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->collect('ids')->filter()->all();
        $removed = $ids ? Activity::query()->whereKey($ids)->delete() : 0;

        return back()->with('flash', $removed
            ? $removed.' '.str('activity')->plural($removed).' removed.'
            : 'Nothing was selected.');
    }

    private function form(Activity $activity): View
    {
        return view('admin.activities-create', [
            'activity' => $activity,
            'days' => Schedule::DAYS,
            'categories' => self::CATEGORIES,
            'destinations' => Destinations::all()->pluck('name')->values()->all(),
            'statuses' => [
                ['value' => ListingStatus::Active->value, 'label' => 'Active / Published', 'description' => 'Visible & bookable immediately'],
                ['value' => ListingStatus::Draft->value, 'label' => 'Draft', 'description' => 'Save work without releasing'],
            ],
            'cancellationPolicies' => StoreActivityRequest::CANCELLATION_POLICIES,
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(StoreActivityRequest $request, ?Activity $existing = null): array
    {
        $data = $request->safe()->except(['cover', 'gallery', 'remove_photos', 'experiences', 'included', 'excluded', 'discount_percent', 'submit_as']);
        $data['included'] = $this->lines($request->input('included'));
        $data['excluded'] = $this->lines($request->input('excluded'));
        $data['days'] = $request->input('days') ?: null;

        // Rows left blank are unused slots, not empty experiences.
        $data['experiences'] = collect($request->input('experiences', []))
            ->filter(fn (array $row) => filled($row['title'] ?? null) || filled($row['body'] ?? null))
            ->map(fn (array $row) => ['title' => (string) ($row['title'] ?? ''), 'body' => (string) ($row['body'] ?? '')])
            ->values()
            ->all() ?: null;
        $data['price_child'] = $data['price_child'] ?? 0;

        // The percentage field is a shortcut: when it is set it decides the struck-through price,
        // so the badge the guest sees is exactly what the admin typed (works without JavaScript too).
        $percent = (int) $request->input('discount_percent', 0);

        if ($percent > 0 && ($data['price_adult'] ?? 0) > 0) {
            $data['price_was'] = (int) round($data['price_adult'] / (1 - $percent / 100) / 1000) * 1000;
        }

        $data['instant_confirmation'] = $request->boolean('instant_confirmation');
        $data['dual_pricing'] = false;
        $data['price_foreign'] = null;
        $data['is_public'] = $request->boolean('is_public');
        $data['rating'] = $request->filled('rating') ? (float) $request->input('rating') : 0;
        $data['publish_at'] = null;

        // The editor stores a small subset of HTML; the card blurb and intro stay plain text.
        $data['description'] = RichText::clean($data['description']);
        $data['summary'] = filled($data['summary'] ?? null) ? RichText::clean($data['summary']) : $data['description'];
        $data['intro'] = $data['intro'] ?? Str::of(RichText::plain($data['description']))->split('/(?<=\.)\s+/')->first(default: '');

        if ($cover = Uploads::store($request->file('cover'), 'activities')) {
            $data['image'] = $cover;
        }

        // Uploads are added to the gallery; photos only disappear when they were ticked for removal.
        $dropped = $request->input('remove_photos', []);
        $kept = collect($existing?->gallery ?? [])
            ->reject(fn (array $photo) => in_array($photo['image'], $dropped, true))
            ->values()
            ->all();

        $gallery = [...$kept, ...Uploads::gallery($request->file('gallery'), 'activities', $request->string('name')->value())];

        if ($gallery !== [] || $dropped !== []) {
            $data['gallery'] = $gallery;
        }

        return $data;
    }

    /** @return list<string> */
    private function lines(?string $text): array
    {
        // Chips arrive comma separated; pasted text may still use one item per line.
        return collect(preg_split('/[\r\n,]+/', (string) $text))->map(fn (string $line) => trim($line))->filter()->values()->all();
    }
}
