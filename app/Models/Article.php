<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Models\Concerns\HasSlug;
use App\Support\ImagePath;
use App\Support\RichText;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title', 'slug', 'category', 'excerpt', 'meta_title', 'meta_description', 'meta_keywords', 'subtitle', 'lead', 'lead_follow', 'body', 'content', 'image',
    'hero_caption', 'hero_alt', 'reader_segment', 'author_id', 'author_name', 'author_role', 'read_time_minutes', 'views', 'tags', 'is_featured',
    'embed_booking_widget', 'widget_route', 'status', 'published_at',
])]
class Article extends Model
{
    use HasFactory, HasSlug;

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'meta_keywords' => 'array',
            'tags' => 'array',
            'is_featured' => 'boolean',
            'embed_booking_widget' => 'boolean',
            'status' => ArticleStatus::class,
            'published_at' => 'datetime',
        ];
    }

    protected function slugSource(): string
    {
        return $this->title;
    }

    public function writer(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /*
     * The byline columns are a snapshot taken when the article was saved. Once an
     * author record is linked it owns those details, so renaming an author or
     * changing their role updates every article they wrote.
     */
    protected function authorName(): Attribute
    {
        return Attribute::get(fn (?string $value) => $this->writer?->name ?? $value);
    }

    protected function authorRole(): Attribute
    {
        return Attribute::get(fn (?string $value) => $this->writer?->role ?? $value);
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::Published)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), fn (Builder $q) => $q->where(fn (Builder $w) => $w
            ->where('title', 'like', "%{$term}%")
            ->orWhere('excerpt', 'like', "%{$term}%")
            ->orWhere('category', 'like', "%{$term}%")));
    }

    /** SEO title, falling back to the headline when the admin left it blank. */
    protected function seoTitle(): Attribute
    {
        return Attribute::get(fn () => filled($this->meta_title) ? $this->meta_title : $this->title);
    }

    /** SEO description, falling back to the card excerpt. */
    protected function seoDescription(): Attribute
    {
        return Attribute::get(fn () => filled($this->meta_description) ? $this->meta_description : $this->excerpt);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => ImagePath::url($this->image, 'articles'));
    }

    protected function readTimeLabel(): Attribute
    {
        return Attribute::get(fn () => $this->read_time_minutes.' min read');
    }

    protected function dateLabel(): Attribute
    {
        return Attribute::get(fn () => ($this->published_at ?? $this->created_at)?->format('M d, Y'));
    }

    protected function longDateLabel(): Attribute
    {
        return Attribute::get(fn () => ($this->published_at ?? $this->created_at)?->format('F j, Y'));
    }

    protected function viewsLabel(): Attribute
    {
        return Attribute::get(fn () => $this->views >= 1000
            ? rtrim(rtrim(number_format($this->views / 1000, 1), '0'), '.').'k views'
            : $this->views.' views');
    }

    /**
     * The body as safe HTML with an id on every heading, plus the headings
     * themselves — what the page renders and what the table of contents links to.
     *
     * @return array{html: string, items: list<array{label: string, anchor: string, level: int}>}
     */
    protected function bodyOutline(): Attribute
    {
        return Attribute::get(fn () => RichText::outline(RichText::clean($this->body, RichText::ALLOWED_ARTICLE)))
            ->shouldCache();
    }

    protected function bodyHtml(): Attribute
    {
        return Attribute::get(fn () => $this->body_outline['html']);
    }

    /**
     * The mobile layout renders the same body, so its headings take an "m-" prefix:
     * duplicate ids would send every table-of-contents link to the hidden copy.
     */
    protected function mobileBodyHtml(): Attribute
    {
        return Attribute::get(fn () => RichText::outline(RichText::clean($this->body, RichText::ALLOWED_ARTICLE), 'm-')['html'])
            ->shouldCache();
    }

    /** Headings from the body; the seeded long-form articles keep their own list. */
    protected function tocItems(): Attribute
    {
        return Attribute::get(fn () => $this->has_structured_content ? $this->toc : $this->body_outline['items']);
    }

    /** Author initials for the console avatar. */
    protected function authorInitials(): Attribute
    {
        return Attribute::get(fn () => collect(explode(' ', $this->author_name))
            ->filter(fn ($p) => ! str_ends_with($p, '.'))
            ->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->implode(''));
    }

    /*
     * Aliases the editorial views were written against.
     */
    protected function author(): Attribute
    {
        return Attribute::get(fn () => $this->author_name);
    }

    protected function readTime(): Attribute
    {
        return Attribute::get(fn () => $this->read_time_label);
    }

    protected function date(): Attribute
    {
        return Attribute::get(fn () => $this->long_date_label);
    }

    protected function href(): Attribute
    {
        return Attribute::get(fn () => route('articles.show', $this));
    }

    protected function hasStructuredContent(): Attribute
    {
        return Attribute::get(fn () => filled(data_get($this->content, 'ports.rows')));
    }

    protected function toc(): Attribute
    {
        return Attribute::get(fn () => $this->block('toc'));
    }

    protected function ports(): Attribute
    {
        return Attribute::get(fn () => $this->block('ports', ['headers' => [], 'rows' => []]));
    }

    protected function timetables(): Attribute
    {
        return Attribute::get(fn () => $this->block('timetables'));
    }

    protected function luggage(): Attribute
    {
        return Attribute::get(fn () => $this->block('luggage'));
    }

    protected function advice(): Attribute
    {
        return Attribute::get(fn () => $this->block('advice'));
    }

    protected function arrival(): Attribute
    {
        return Attribute::get(fn () => $this->block('arrival'));
    }

    protected function popular(): Attribute
    {
        return Attribute::get(fn () => $this->block('popular'));
    }

    /** Typed access to a structured content block, e.g. $article->block('toc'). */
    public function block(string $key, mixed $default = []): mixed
    {
        return data_get($this->content, $key, $default);
    }
}
