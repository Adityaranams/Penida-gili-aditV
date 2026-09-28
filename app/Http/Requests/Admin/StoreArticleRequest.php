<?php

namespace App\Http\Requests\Admin;

use App\Enums\ArticleStatus;
use App\Models\Author;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $tags = collect(preg_split('/[,\s]+/', (string) $this->input('tags', '')))
            ->filter()
            ->map(fn (string $tag) => '#'.ltrim($tag, '#'))
            ->unique()
            ->values()
            ->all();

        $keywords = collect(preg_split('/\s*,\s*/', (string) $this->input('meta_keywords', '')))
            ->map(fn (string $k) => trim($k))
            ->filter()
            ->unique()
            ->values()
            ->all();

        // The header buttons override the Publishing Settings radios.
        $status = match ($this->input('submit_as')) {
            'draft' => ArticleStatus::Draft->value,
            'publish' => ArticleStatus::Published->value,
            default => $this->input('status'),
        };

        $this->merge([
            'tags' => $tags,
            'meta_keywords' => $keywords,
            'status' => $status,
            'embed_booking_widget' => $this->boolean('embed_booking_widget'),
        ]);
    }

    public function rules(): array
    {
        $articleId = $this->route('article')?->id;

        return [
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['required', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'array', 'max:15'],
            'meta_keywords.*' => ['string', 'max:40'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'category' => ['required', 'string', 'max:60'],
            'body' => ['required', 'string'],
            'hero_caption' => ['nullable', 'string', 'max:200'],
            'hero_alt' => ['nullable', 'string', 'max:200'],
            'reader_segment' => ['nullable', 'string', 'max:80'],
            'embed_booking_widget' => ['nullable', 'boolean'],
            'widget_route' => ['nullable', 'string', 'max:120'],
            'submit_as' => ['nullable', Rule::in(['draft', 'publish'])],
            // AUTHOR bar: pick an existing author, or "new" plus a typed name.
            'author_id' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value !== 'new' && ! Author::query()->whereKey($value)->exists()) {
                    $fail('Pick an author from the list.');
                }
            }],
            'author_name' => ['nullable', 'string', 'max:120', Rule::requiredIf(fn () => $this->input('author_id') === 'new' || blank($this->input('author_id')))],
            'author_role' => ['nullable', 'string', 'max:120'],
            'author_credential' => ['nullable', 'string', 'max:80'],
            'author_bio' => ['nullable', 'string', 'max:1000'],
            'author_photo' => ['nullable', 'image', 'max:4096'],
            'read_time_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:40'],
            'status' => ['required', Rule::enum(ArticleStatus::class)],
            'published_at' => ['nullable', 'date', Rule::requiredIf(fn () => $this->input('status') === ArticleStatus::Scheduled->value)],
            'is_featured' => ['nullable', 'boolean'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
