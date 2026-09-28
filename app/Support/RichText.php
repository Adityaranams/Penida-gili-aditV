<?php

namespace App\Support;

/**
 * The console description editor stores a deliberately small slice of HTML:
 * bold, italic, underline and lists. Everything else is stripped on the way in,
 * so the stored value is always safe to print with {!! !!}.
 */
final class RichText
{
    public const ALLOWED = '<strong><b><em><i><u><ul><ol><li><p><br>';

    public static function clean(?string $html): string
    {
        // strip_tags() keeps the *contents* of a <script>; drop those blocks whole first.
        $html = preg_replace('#<(script|style|iframe)\b[^>]*>.*?</\1>#isu', '', (string) $html) ?? (string) $html;

        $clean = strip_tags($html, self::ALLOWED);

        // contenteditable leaves empty wrappers behind when the admin clears a line.
        $clean = preg_replace('#<(p|li|ul|ol)>\s*(&nbsp;|\s)*</\1>#iu', '', $clean) ?? $clean;

        return trim($clean);
    }

    /** The same copy as plain text, for cards, meta tags and excerpts. */
    public static function plain(?string $html): string
    {
        $text = preg_replace('#<(/p|/li|br\s*/?)>#iu', ' ', (string) $html) ?? (string) $html;

        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text))) ?? '');
    }
}
