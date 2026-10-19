<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

/**
 * Contact details the console can change, layered over config/penida.php.
 *
 * Every page still reads config('penida.booking.*'); this class only decides
 * what those keys hold. apply() runs once per request from AppServiceProvider
 * and overwrites them with whatever the console saved, so a saved number
 * reaches all twenty-odd call sites without touching one of them.
 *
 * A missing row means "no override" and the config default stands, which is
 * also what happens before the migration runs or if the table cannot be read
 * — the site must still boot on a fresh clone.
 */
class SiteSettings
{
    /** Setting key => the config key it overrides. */
    public const KEYS = [
        'booking_whatsapp' => 'penida.booking.whatsapp',
        'booking_email' => 'penida.booking.email',
        'booking_notify_email' => 'penida.booking.notify_email',
    ];

    private const CACHE_KEY = 'site-settings';

    /** The saved overrides, empty when nothing has been saved yet. */
    public static function stored(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return Setting::query()
                    ->whereIn('key', array_keys(self::KEYS))
                    ->pluck('value', 'key')
                    ->all();
            } catch (QueryException) {
                // No settings table yet (fresh clone, migrations pending).
                return [];
            }
        });
    }

    /** What each field should show in the console: the override, else the config default. */
    public static function current(): array
    {
        $stored = self::stored();

        return collect(self::KEYS)
            ->map(fn (string $config, string $key) => $stored[$key] ?? config($config))
            ->all();
    }

    /** The config values as they ship, before any override was laid over them. */
    private static array $defaults = [];

    /** Let the saved values stand in for the config defaults for this request. */
    public static function apply(): void
    {
        $stored = self::stored();

        foreach (self::KEYS as $key => $config) {
            // Remember the shipped value once, so clearing a setting can put it
            // back rather than leaving the overridden value in place.
            self::$defaults[$key] ??= config($config);

            $value = $stored[$key] ?? null;

            config([$config => $value === null || $value === '' ? self::$defaults[$key] : $value]);
        }
    }

    /** Save the console's values; a blank one is removed so the config default returns. */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! isset(self::KEYS[$key])) {
                continue;
            }

            $value = is_string($value) ? trim($value) : $value;

            if ($value === null || $value === '') {
                Setting::query()->whereKey($key)->delete();

                continue;
            }

            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);

        // The request that saved them should already see the new values.
        self::apply();
    }
}
