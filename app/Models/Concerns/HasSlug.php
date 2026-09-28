<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates a unique slug from the model's display column on first save.
 * Models opt in by defining `slugSource()`.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (Model $model): void {
            if (blank($model->slug)) {
                $model->slug = $model->uniqueSlug(Str::slug($model->slugSource()));
            }
        });
    }

    abstract protected function slugSource(): string;

    /**
     * The slug this record should carry for the given title — unchanged when the
     * title still produces the one it already has, so existing links survive.
     */
    public function slugFor(?string $title): string
    {
        $base = Str::slug((string) $title);

        return $this->slug === $base || str_starts_with((string) $this->slug, $base.'-')
            ? $this->slug
            : $this->uniqueSlug($base);
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base ?: Str::random(8);
        $i = 2;

        while (static::query()->whereKeyNot($this->getKey())->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
