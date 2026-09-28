<?php

namespace App\Models;

use App\Enums\ListingStatus;
use App\Support\ImagePath;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'boat_operator_id', 'name', 'code', 'type', 'description', 'capacity', 'rating', 'top_speed_knots', 'engine',
    'facilities', 'image', 'gallery', 'status', 'inspected_at',
])]
class Vessel extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'facilities' => 'array',
            'gallery' => 'array',
            'status' => ListingStatus::class,
            'inspected_at' => 'date',
        ];
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(BoatOperator::class, 'boat_operator_id');
    }

    /** Testimonials written for this boat only. */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable')->latest('experienced_at');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => ImagePath::url($this->image, 'boats'));
    }

    /**
     * Gallery entries with resolved URLs, falling back to the cover photo.
     *
     * @return list<array{image: string, alt: string, url: string}>
     */
    protected function galleryPhotos(): Attribute
    {
        return Attribute::get(function () {
            $items = collect($this->gallery ?: [])
                ->map(fn (array $photo) => $photo + ['url' => ImagePath::url($photo['image'], 'boats')]);

            return $items->isNotEmpty()
                ? $items->values()->all()
                : [['image' => $this->image, 'alt' => $this->name, 'url' => $this->image_url]];
        });
    }

    /** Distinct port pairs this boat currently sails. */
    protected function routeCount(): Attribute
    {
        return Attribute::get(fn () => $this->schedules
            ->where('status', ListingStatus::Active)
            ->unique(fn ($schedule) => $schedule->from_port_id.'-'.$schedule->to_port_id)
            ->count());
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', ListingStatus::Active);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /** Next free code in the SFB-000 sequence. */
    public static function nextCode(): string
    {
        $last = static::query()->where('code', 'like', 'SFB-%')->orderByDesc('code')->value('code');
        $n = $last ? ((int) substr($last, 4)) + 1 : 1;

        return sprintf('SFB-%03d', $n);
    }
}
