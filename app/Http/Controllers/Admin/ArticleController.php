<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Models\Article;
use App\Models\Author;
use App\Support\RichText;
use App\Support\Uploads;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public const CATEGORIES = ['Travel Guides', 'Boat Tips', 'Activities', 'Culture', 'Hotels', 'Fast Boat Transfers'];

    /** Target Reader Segment options (Figma 1:8139). */
    public const SEGMENTS = ['First-time Island Travelers', 'Returning Visitors', 'Families with Children', 'Divers & Snorkelers', 'Backpackers', 'Luxury Travelers'];

    /** Article listing — Figma node 1:9637. */
    public function index(Request $request): View
    {
        $articles = Article::query()
            ->with('writer')
            ->search($request->string('q')->value())
            // Match the author record when there is one, falling back to the stored byline.
            ->when($request->filled('author'), fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('writer', fn ($a) => $a->where('name', $request->string('author')->value()))
                ->orWhere(fn ($x) => $x->whereNull('author_id')->where('author_name', $request->string('author')->value()))))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')->value()))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->value()))
            ->latest('published_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.articles', [
            'articles' => $articles,
            'filters' => $request->only(['q', 'author', 'category', 'status']),
            // Every author on file, not just the ones who already have an article.
            'authors' => Author::query()->orderBy('name')->pluck('name')->all(),
            'categories' => self::CATEGORIES,
        ]);
    }

    /** Stores a photo dropped into the body editor and hands back its URL. */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate(['image' => ['required', 'image', 'max:10240']]);

        // A root-relative path, not an absolute one: the body is stored as written,
        // so a URL carrying this machine's host would break for every visitor
        // reaching the site by any other address (a tunnel, the LAN, production).
        $url = Storage::disk('public')->url(Uploads::store($request->file('image'), 'articles'));

        return response()->json([
            'url' => parse_url($url, PHP_URL_PATH) ?: $url,
        ]);
    }

    /** Add New Article — Figma node 1:8059. */
    public function create(): View
    {
        return $this->form(new Article(['status' => ArticleStatus::Published, 'read_time_minutes' => 5]));
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $article = Article::query()->create($this->payload($request));

        return redirect()->route('admin.articles')->with('flash', "\"{$article->title}\" saved.");
    }

    public function edit(Article $article): View
    {
        return $this->form($article);
    }

    public function update(StoreArticleRequest $request, Article $article): RedirectResponse
    {
        $article->update($this->payload($request, $article));

        return redirect()->route('admin.articles')->with('flash', "\"{$article->title}\" updated.");
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles')->with('flash', 'Article removed.');
    }

    /** Delete everything ticked in the listing. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->collect('ids')->filter()->all();
        $removed = $ids ? Article::query()->whereKey($ids)->delete() : 0;

        return back()->with('flash', $removed
            ? $removed.' '.str('article')->plural($removed).' removed.'
            : 'Nothing was selected.');
    }

    private function form(Article $article): View
    {
        return view('admin.articles-create', [
            'article' => $article,
            'categories' => self::CATEGORIES,
            'segments' => self::SEGMENTS,
            'authors' => Author::query()->orderBy('name')->get(),
            'publishModes' => [
                ['value' => ArticleStatus::Published->value, 'label' => 'Publish Immediately', 'description' => 'Live to all passenger channels right away'],
                ['value' => ArticleStatus::Draft->value, 'label' => 'Save as Draft', 'description' => 'Internal review without public URL'],
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(StoreArticleRequest $request, ?Article $existing = null): array
    {
        $data = $request->safe()->except(['cover', 'submit_as']);
        // Flags without a control on the Figma form keep their stored value.
        $data['is_featured'] = $request->has('is_featured') ? $request->boolean('is_featured') : (bool) $existing?->is_featured;
        $data['embed_booking_widget'] = $request->has('embed_booking_widget') ? $request->boolean('embed_booking_widget') : (bool) $existing?->embed_booking_widget;

        // AUTHOR bar: an existing author fills the byline; "new" creates one from the typed name.
        $author = $request->input('author_id') === 'new' || blank($request->input('author_id'))
            ? Author::query()->firstOrCreate(['name' => trim($request->input('author_name'))])
            : Author::query()->findOrFail($request->input('author_id'));

        // The profile fields belong to the author, not the article, so the byline and
        // the card stay the same wherever that author is credited.
        $author->fill(array_filter([
            'role' => $request->input('author_role'),
            'credential' => $request->input('author_credential'),
            'bio' => $request->input('author_bio'),
            'photo' => Uploads::store($request->file('author_photo'), 'authors'),
        ], fn ($value) => $value !== null))->save();

        $data['author_id'] = $author->id;
        $data['author_name'] = $author->name;
        $data['author_role'] = $author->role;
        // The body editor stores a small subset of HTML; read time follows the words in it.
        $data['body'] = RichText::clean($data['body'], RichText::ALLOWED_ARTICLE);
        $data['read_time_minutes'] = $data['read_time_minutes'] ?? max(1, (int) ceil(str_word_count(RichText::plain($data['body'])) / 200));

        $status = ArticleStatus::from($data['status']);
        $data['published_at'] = match ($status) {
            ArticleStatus::Published => $existing?->published_at ?? now(),
            ArticleStatus::Scheduled => $data['published_at'],
            ArticleStatus::Draft => null,
        };

        // The address always follows the title, so a renamed article keeps a matching link.
        unset($data['slug']);

        if ($existing) {
            $data['slug'] = $existing->slugFor($data['title']);
        }

        if ($cover = Uploads::store($request->file('cover'), 'articles')) {
            $data['image'] = $cover;
        }

        return $data;
    }
}
