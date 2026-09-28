<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * The console editors store a deliberately small slice of HTML: headings,
 * emphasis, lists, quotes and links. Everything else — other tags, every
 * attribute except a link's href, and any non-http(s) href — is stripped on the
 * way in, so the stored value is always safe to print with {!! !!}.
 */
final class RichText
{
    /** Short form (descriptions): emphasis and lists only. */
    public const ALLOWED = '<strong><b><em><i><u><s><strike><ul><ol><li><p><br>';

    /** Long form (article bodies): the short form plus headings, quotes, links and photos. */
    public const ALLOWED_ARTICLE = self::ALLOWED.'<h2><h3><blockquote><a><img>';

    public static function clean(?string $html, string $allowed = self::ALLOWED): string
    {
        // strip_tags() keeps the *contents* of a <script>; drop those blocks whole first.
        $html = preg_replace('#<(script|style|iframe)\b[^>]*>.*?</\1>#isu', '', (string) $html) ?? (string) $html;

        $clean = strip_tags($html, $allowed);
        $clean = self::stripAttributes($clean);

        // contenteditable leaves empty wrappers behind when the admin clears a line.
        $clean = preg_replace('#<(p|li|ul|ol|h2|h3|blockquote)>\s*(&nbsp;|\s)*</\1>#iu', '', $clean) ?? $clean;

        return trim($clean);
    }

    /**
     * Give every heading an id and list them, so an article can carry its own
     * table of contents without the editor having to write anchors by hand.
     *
     * @return array{html: string, items: list<array{label: string, anchor: string, level: int}>}
     */
    public static function outline(?string $html, string $prefix = ''): array
    {
        $items = [];
        $used = [];

        $withIds = preg_replace_callback('#<h([23])>(.*?)</h\1>#isu', function (array $match) use (&$items, &$used, $prefix): string {
            $label = self::plain($match[2]);

            if ($label === '') {
                return $match[0];
            }

            $anchor = $prefix.(Str::slug($label) ?: 'section');
            $used[$anchor] = ($used[$anchor] ?? 0) + 1;

            if ($used[$anchor] > 1) {
                $anchor .= '-'.$used[$anchor];
            }

            $items[] = ['label' => $label, 'anchor' => $anchor, 'level' => (int) $match[1]];

            return '<h'.$match[1].' id="'.$anchor.'">'.$match[2].'</h'.$match[1].'>';
        }, (string) $html) ?? (string) $html;

        return ['html' => $withIds, 'items' => $items];
    }

    /** The same copy as plain text, for cards, meta tags and excerpts. */
    public static function plain(?string $html): string
    {
        $text = preg_replace('#<(/p|/li|/h2|/h3|/blockquote|br\s*/?)>#iu', ' ', (string) $html) ?? (string) $html;

        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text))) ?? '');
    }

    /**
     * Drop every attribute the editors have no use for. Only a link's href and a
     * photo's src/alt survive, and only when the URL points somewhere safe —
     * never javascript: or data:.
     */
    private static function stripAttributes(string $html): string
    {
        return preg_replace_callback('#<([a-z][a-z0-9]*)\b([^>]*)>#iu', function (array $match): string {
            $tag = mb_strtolower($match[1]);
            $attributes = $match[2];

            if ($tag === 'a') {
                $url = self::attribute($attributes, 'href');

                return self::safeUrl($url)
                    ? '<a href="'.e($url, false).'" target="_blank" rel="noopener nofollow">'
                    : '<a>';
            }

            // The only class the editors can set: the drop cap that opens an article.
            if ($tag === 'p' && self::attribute($attributes, 'class') === 'drop-cap') {
                return '<p class="drop-cap">';
            }

            if ($tag === 'img') {
                $url = self::attribute($attributes, 'src');
                $alt = self::attribute($attributes, 'alt');

                return self::safeUrl($url)
                    ? '<img src="'.e($url, false).'" alt="'.e($alt, false).'">'
                    : '';
            }

            return "<{$tag}>";
        }, $html) ?? $html;
    }

    private static function attribute(string $attributes, string $name): string
    {
        preg_match('#\b'.$name.'\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#iu', $attributes, $found);

        return trim($found[1] ?? '', "\"' ");
    }

    private static function safeUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        // Relative paths and anchors are fine; anything with a scheme must be http(s) or mailto.
        return (bool) preg_match('#^(https?://|mailto:|/|\#)#i', $url);
    }
}
