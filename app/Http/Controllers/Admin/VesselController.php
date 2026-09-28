<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVesselRequest;
use App\Models\BoatOperator;
use App\Models\Vessel;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VesselController extends Controller
{
    public const FACILITIES = ['Air Conditioning', 'Toilet', 'Life Jackets', 'Insurance', 'Sound System', 'Free Water'];

    /** Boat listing — Figma node 1:6901. */
    public function index(Request $request): View
    {
        $vessels = Vessel::query()
            ->with('operator')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->string('q').'%')
                ->orWhere('code', 'like', '%'.$request->string('q').'%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->value()))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.boats', ['boats' => $vessels, 'filters' => $request->only(['q', 'status'])]);
    }

    /** Add New Boat — Figma node 1:7132. */
    public function create(): View
    {
        // A brand-new boat starts with nothing ticked; the operator chooses what it carries.
        return $this->form(new Vessel(['status' => ListingStatus::Active, 'facilities' => []]));
    }

    public function store(StoreVesselRequest $request): RedirectResponse
    {
        // The Figma form has no operator picker: the fleet belongs to the (single) operator on file.
        $vessel = Vessel::query()->create($this->payload($request) + [
            'code' => $request->input('code') ?: Vessel::nextCode(),
            'boat_operator_id' => $request->input('boat_operator_id') ?: BoatOperator::query()->orderBy('id')->value('id'),
        ]);

        $this->syncTestimonials($request, $vessel);

        return redirect()->route('admin.boats')->with('flash', "{$vessel->name} added to the fleet.");
    }

    public function edit(Vessel $vessel): View
    {
        return $this->form($vessel);
    }

    public function update(StoreVesselRequest $request, Vessel $vessel): RedirectResponse
    {
        $vessel->update($this->payload($request, $vessel) + array_filter(['code' => $request->input('code')]));

        $this->syncTestimonials($request, $vessel);

        return redirect()->route('admin.boats')->with('flash', "{$vessel->name} updated.");
    }

    public function destroy(Vessel $vessel): RedirectResponse
    {
        $vessel->delete();

        return redirect()->route('admin.boats')->with('flash', "{$vessel->name} removed.");
    }

    /** Delete everything ticked in the listing. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->collect('ids')->filter()->all();
        $removed = $ids ? Vessel::query()->whereKey($ids)->delete() : 0;

        return back()->with('flash', $removed
            ? $removed.' '.str('boat')->plural($removed).' removed.'
            : 'Nothing was selected.');
    }

    private function form(Vessel $vessel): View
    {
        return view('admin.boats-create', [
            'vessel' => $vessel,
            'facilities' => collect(self::FACILITIES)->map(fn ($label) => [
                'label' => $label,
                'checked' => in_array($label, old('facilities', $vessel->facilities ?? []), true),
            ])->all(),
            'types' => ['Boat'],
            'publishModes' => [
                ['value' => 'publish', 'label' => 'Publish Immediately', 'description' => 'Visible in the fleet and available for schedules right away'],
                ['value' => 'draft', 'label' => 'Save as Draft', 'description' => 'Keep the boat hidden until you are ready'],
            ],
            'operationalStatuses' => [
                ListingStatus::Active->value => 'Active (Ready for Routes)',
                ListingStatus::Inactive->value => 'Non-Active (Maintenance)',
            ],
        ]);
    }

    /**
     * Save the "Guest Testimonials" rows: update the ones already on file,
     * create the newly typed ones and drop whatever the admin ticked.
     */
    private function syncTestimonials(StoreVesselRequest $request, Vessel $vessel): void
    {
        foreach ($request->input('testimonials', []) as $row) {
            $review = isset($row['id']) ? $vessel->reviews()->find($row['id']) : null;

            if ($review && filter_var($row['remove'] ?? false, FILTER_VALIDATE_BOOL)) {
                $review->delete();

                continue;
            }

            // A row left completely blank is just an unused slot.
            if (blank($row['name'] ?? null) || blank($row['quote'] ?? null)) {
                continue;
            }

            $attributes = [
                'name' => $row['name'],
                'quote' => $row['quote'],
                'stars' => (int) ($row['stars'] ?? 5),
                'experienced_at' => filled($row['experienced_at'] ?? null) ? $row['experienced_at'].'-01' : null,
                'is_published' => true,
            ];

            $review ? $review->update($attributes) : $vessel->reviews()->create($attributes);
        }
    }

    /** @return array<string, mixed> */
    private function payload(StoreVesselRequest $request, ?Vessel $vessel = null): array
    {
        $data = $request->safe()->except(['cover', 'photos', 'remove_photos', 'testimonials', 'code', 'publish']);
        $data['facilities'] = $request->input('facilities', []);

        // "Save as Draft" hides the boat regardless of the operational status picked below it.
        $data['status'] = $request->input('publish') === 'draft'
            ? ListingStatus::Draft->value
            : $request->input('status', ListingStatus::Active->value);

        if ($cover = Uploads::store($request->file('cover'), 'vessels')) {
            $data['image'] = $cover;
        }

        // Uploads are added to the gallery; photos only disappear when they were ticked for removal.
        $dropped = $request->input('remove_photos', []);
        $kept = collect($vessel?->gallery ?? [])
            ->reject(fn (array $photo) => in_array($photo['image'], $dropped, true))
            ->values()
            ->all();

        $gallery = [...$kept, ...Uploads::gallery($request->file('photos'), 'vessels', $request->string('name')->value())];

        if ($gallery !== [] || $dropped !== []) {
            $data['gallery'] = $gallery;
        }

        return $data;
    }
}
