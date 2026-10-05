<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\File;

/**
 * Demo rows borrow whatever photos the project already has: admin uploads on
 * the public disk first, falling back to the design assets under
 * public/images/<folder>. Both forms are valid stored paths (App\Support\ImagePath).
 */
trait PicksDemoPhotos
{
    /**
     * @param  list<string>  $fallback  filenames under public/images/<folder>
     * @return list<string>
     */
    protected function photoPool(string $folder, array $fallback = []): array
    {
        $directory = storage_path('app/public/uploads/'.$folder);

        $uploads = File::isDirectory($directory)
            ? collect(File::files($directory))
                ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
                ->map(fn ($file) => 'uploads/'.$folder.'/'.$file->getFilename())
                ->sort()
                ->values()
                ->all()
            : [];

        return $uploads ?: $fallback;
    }

    /** @param  list<string>  $pool */
    protected function photo(array $pool, int $index): ?string
    {
        return $pool === [] ? null : $pool[$index % count($pool)];
    }

    /**
     * A gallery that does not repeat the cover, taking the photos that follow it.
     *
     * @param  list<string>  $pool
     * @return list<array{image: string, alt: string}>
     */
    protected function galleryFor(array $pool, int $index, string $alt, int $count): array
    {
        if ($pool === []) {
            return [];
        }

        $gallery = [];

        for ($i = 1; $i <= $count; $i++) {
            $gallery[] = ['image' => $pool[($index + $i) % count($pool)], 'alt' => $alt];
        }

        return $gallery;
    }
}
