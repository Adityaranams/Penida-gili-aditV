<?php

namespace App\Models;

use App\Enums\ListingStatus;
use App\Models\Concerns\HasReviews;
use App\Models\Concerns\HasSlug;
use App\Support\ImagePath;
use App\Support\Money;
use App\Support\RichText;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'name', 'slug', 'badge', 'category', 'location', 'place_label', 'opens_at', 'closes_at', 'duration_label',
    'description', 'intro', 'summary', 'summary_image', 'image', 'gallery', 'highlights', 'experiences',
    'included', 'excluded', 'important_notes', 'days', 'price_adult', 'price_child', 'price_was', 'dual_pricing', 'price_foreign',
    'price_note', 'max_daily_capacity', 'instant_confirmation', 'cancellation_policy', 'rating', 'review_count', 'sold_count',
    'status', 'is_public', 'publish_at',
])]
class Activity extends Model
{
    use HasFactory, HasReviews, HasSlug;

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'gallery' => 'array',
            'highlights' => 'array',
            'experiences' => 'array',
            'included' => 'array',
            'excluded' => 'array',
            'days' => 'array',
            'price_adult' => 'integer',
            'price_child' => 'integer',
            'price_was' => 'integer',
            'price_foreign' => 'integer',
            'max_daily_capacity' => 'integer',
            'instant_confirmation' => 'boolean',
            'dual_pricing' => 'boolean',
            'is_public' => 'boolean',
            'publish_at' => 'datetime',
            'status' => ListingStatus::class,
        ];
    }

    protected function slugSource(): string
    {
        return $this->name;
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', ListingStatus::Active);
    }

    /**
     * Gallery entries with resolved URLs; falls back to the cover so detail
     * pages always have a lead photo.
     *
     * @return list<array{image: string, alt: string, url: string, more?: string}>
     */
    protected function galleryPhotos(): Attribute
    {
        return Attribute::get(function () {
            $items = collect($this->gallery ?: [])
                ->map(fn (array $photo) => $photo + ['url' => ImagePath::url($photo['image'], 'activities/detail')])
                ->values();

            $cover = ['image' => $this->image, 'alt' => $this->name, 'url' => ImagePath::url($this->image, 'activities')];

            if ($items->isEmpty()) {
                $items = collect([$cover]);
            }

            // The detail collage has three frames; repeat what we have rather than leave holes.
            $pool = $items->all();
            while (count($pool) < 3) {
                $pool[] = $pool[(count($pool) - 1) % count($items)];
            }

            return $pool;
        });
    }

    /**
     * The three badges above the tabs. Seeded rows carry their own; anything added
     * in the console derives them from the policies it was saved with.
     *
     * @return list<array{icon: string, title: string, note: string}>
     */
    protected function highlightItems(): Attribute
    {
        return Attribute::get(function (): array {
            if ($this->highlights) {
                return $this->highlights;
            }

            $cancellation = match ($this->cancellation_policy) {
                'free_24h' => ['Free cancellation', 'Up to 24 hours'],
                'free_48h' => ['Free cancellation', 'Up to 48 hours'],
                'partial' => ['Partial refund', '50% within 24 hours'],
                default => null,
            };

            return array_values(array_filter([
                $cancellation ? ['icon' => 'cancellation.svg', 'title' => $cancellation[0], 'note' => $cancellation[1]] : null,
                $this->instant_confirmation ? ['icon' => 'confirmation.svg', 'title' => 'Instant confirmation', 'note' => 'Quick & easy'] : null,
                ['icon' => 'support.svg', 'title' => '24/7 Support', 'note' => 'We are ready to help'],
            ]));
        });
    }

    /** Wide photo under the Summary heading; falls back to the cover. */
    protected function summaryImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->summary_image
            ? ImagePath::url($this->summary_image, 'activities/detail')
            : $this->image_url);
    }

    /** Description without markup — what the cards and excerpts use. */
    protected function plainDescription(): Attribute
    {
        return Attribute::get(fn () => RichText::plain($this->description));
    }

    /** Summary in the safe HTML subset the console editor stores; falls back to the description. */
    protected function summaryHtml(): Attribute
    {
        return Attribute::get(fn () => RichText::clean($this->summary ?: $this->description));
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => ImagePath::url($this->image, 'activities'));
    }

    /** "08:00 - 18:00" */
    protected function hoursLabel(): Attribute
    {
        return Attribute::get(fn () => $this->opens_at && $this->closes_at
            ? Carbon::parse($this->opens_at)->format('H:i').' - '.Carbon::parse($this->closes_at)->format('H:i')
            : null);
    }

    /** Console category pill glyph (Figma 1:9970); matched loosely so new categories still get one. */
    protected function categoryIcon(): Attribute
    {
        return Attribute::get(function (): string {
            $category = mb_strtolower((string) $this->category);

            return match (true) {
                str_contains($category, 'water') => 'cat-water-sports.svg',
                str_contains($category, 'wildlife'), str_contains($category, 'nature'), str_contains($category, 'adventure') => 'cat-wildlife-nature.svg',
                str_contains($category, 'show'), str_contains($category, 'dance') => 'cat-cultural-show.svg',
                str_contains($category, 'culture') => 'cat-photography-culture.svg',
                default => 'cat-photography.svg',
            };
        });
    }

    protected function priceLabel(): Attribute
    {
        return Attribute::get(fn () => Money::idr($this->price_adult));
    }

    protected function priceWasLabel(): Attribute
    {
        return Attribute::get(fn () => $this->price_was ? Money::idr($this->price_was, 'Rp.') : null);
    }

    /** How much cheaper the current price is than the struck-through one, in whole percent. */
    protected function discountPercent(): Attribute
    {
        return Attribute::get(fn () => $this->price_was > $this->price_adult && $this->price_was > 0
            ? (int) round((1 - $this->price_adult / $this->price_was) * 100)
            : 0);
    }

    /** Card meta rows: location, hours, category. */
    protected function meta(): Attribute
    {
        return Attribute::get(fn () => array_values(array_filter([
            ['icon' => 'location.svg', 'label' => $this->place_label ?: $this->location],
            $this->hours_label ? ['icon' => 'clock.svg', 'label' => $this->hours_label] : null,
            ['icon' => 'category.svg', 'label' => $this->category],
        ])));
    }

    /**
     * Anchor tabs on the detail page.
     *
     * @return list<array{label: string, anchor: string}>
     */
    protected function tabs(): Attribute
    {
        return Attribute::get(fn () => array_values(array_filter([
            ['label' => 'Summary', 'anchor' => 'summary'],
            $this->experiences ? ['label' => 'Experiences', 'anchor' => 'experiences'] : null,
            $this->included || $this->excluded ? ['label' => 'Inclusions', 'anchor' => 'inclusions'] : null,
            // A tab only appears when the section it jumps to has something in it.
            filled($this->important_notes) ? ['label' => 'Important Info', 'anchor' => 'important-info'] : null,
        ])));
    }

    /** Detail-page meta uses a different icon set than the cards. */
    protected function detailMeta(): Attribute
    {
        return Attribute::get(fn () => array_values(array_filter([
            ['icon' => 'pin.svg', 'label' => $this->place_label ?: $this->location],
            $this->hours_label ? ['icon' => 'clock.svg', 'label' => $this->hours_label] : null,
            ['icon' => 'camera.svg', 'label' => $this->category],
        ])));
    }
}
