{{-- Figma node 1:8059 — admin Add New Article (also serves Edit). Figma is drawn at ~1.47×; sizes here are scaled down. --}}
@extends('layouts.admin')

@php
    $editing = $article->exists;
    $backHref = route('admin.articles');
    $keywords = old('meta_keywords', $article->meta_keywords ?? []);
    $keywords = is_array($keywords) ? implode(', ', $keywords) : $keywords;
    $tags = old('tags', $article->tags ?? []);
    $tags = is_array($tags) ? implode(', ', $tags) : $tags;
    $icon = fn ($file) => asset('images/icons/admin/article/'.$file);
    $label = 'font-jakarta text-[12px] font-bold uppercase leading-[16px] tracking-[0.6px] text-editorial-ink';
    $sideLabel = 'font-jakarta text-[12px] font-bold uppercase leading-[16px] tracking-[0.6px] text-editorial-body';
    $input = 'w-full rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] px-[13px] py-[11px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial';
    $card = 'rounded-[12px] border border-[#ebeef0] bg-surface shadow-[0_4px_10px_rgba(0,0,0,0.05)]';
    $head = 'flex items-center gap-[10px] border-b border-[#ebeef0] pb-[17px]';
    $tile = 'flex size-[32px] shrink-0 items-center justify-center rounded-[8px] bg-[#d2e4ff]';
    $h2 = 'font-jakarta text-[18px] font-bold leading-[28px] text-editorial-ink';
    $sideH = 'font-jakarta text-[24px] font-semibold leading-[32px] text-editorial-ink';
    $siteHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'penidagili.com';
    $initials = fn ($name) => collect(preg_split('/\s+/', trim((string) $name)))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('') ?: 'PG';
@endphp

@section('title', $editing ? 'Edit Article' : 'Add New Article')

@section('admin-active', 'article')

@section('content')
    <x-admin.form-page
        :heading="$editing ? 'Edit Article' : 'Add New Article'"
        subtitle="Compose editorial guides, maritime safety tips, island port advice, and SEO content for passengers."
        :back-href="$backHref">

        {{-- Header actions (1:8070 / 1:8074) --}}
        <x-slot:actions>
            <button type="submit" form="article-form" name="submit_as" value="draft"
                    class="flex items-center gap-[8px] rounded-[8px] border border-[#c0c7d3] bg-surface px-[21px] py-[11px] font-jakarta text-[14px] font-semibold text-editorial-ink
                           transition-colors duration-300 hover:bg-[#f1f4f6]">
                <x-admin.icon name="form-save.svg" class="size-[13.5px] bg-editorial" />
                Save Draft
            </button>
            <button type="submit" form="article-form" name="submit_as" value="publish"
                    class="flex items-center gap-[8px] rounded-[8px] bg-editorial px-[24px] py-[10px] font-jakarta text-[14px] font-semibold text-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)]
                           transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                <x-admin.icon name="nav-article.svg" class="size-[12px] bg-white" />
                Publish Article
            </button>
        </x-slot:actions>

        <form id="article-form" action="{{ $editing ? route('admin.articles.update', $article) : route('admin.articles.store') }}" method="post" enctype="multipart/form-data"
              class="grid [&>*]:min-w-0 gap-[24px] lg:grid-cols-[minmax(0,68fr)_minmax(0,32fr)]" data-article-form>
            @csrf
            @if ($editing) @method('PUT') @endif

            <div class="flex flex-col gap-[24px]">
                {{-- 1. Article Core Editorial (1:8096) --}}
                <section class="{{ $card }} p-[33px]">
                    <div class="{{ $head }}">
                        <span class="flex items-center gap-[10px]">
                            <span class="{{ $tile }}"><img src="{{ $icon('editor-core.svg') }}" alt="" class="size-[15px]"></span>
                            <h2 class="{{ $h2 }}">Article Core Editorial</h2>
                        </span>
                    </div>

                    <div class="mt-[24px] flex flex-col gap-[20px]">
                        <div class="flex flex-col gap-[8px]">
                            <label for="article-title" class="{{ $label }}">Article Title <span class="text-[#ba1a1a]">*</span></label>
                            <input id="article-title" name="title" value="{{ old('title', $article->title) }}" required placeholder="Complete Guide to Nusa Penida Fast Boat Transfers: Schedules, Ports, and Travel Tips"
                                   class="{{ $input }} p-[13px] text-[16px] font-bold leading-[24px] shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                            @error('title') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-[8px] pb-[6px]">
                            <label for="article-excerpt" class="{{ $label }}">Subtitle / Summary Hook <span class="text-[#ba1a1a]">*</span></label>
                            <textarea id="article-excerpt" name="excerpt" rows="3" required placeholder="Everything travelers need to know about navigating crossings from Sanur Harbor to Banjar Nyuh seamlessly…"
                                      class="{{ $input }} resize-y p-[13px] leading-[23px] shadow-[0_1px_2px_rgba(0,0,0,0.05)]">{{ old('excerpt', $article->excerpt) }}</textarea>
                            @error('excerpt') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>

                        {{-- Metadata Grid (1:8122) --}}
                        <div class="grid [&>*]:min-w-0 gap-[16px] pt-[8px] sm:grid-cols-2">
                            <div class="flex flex-col gap-[8px]">
                                <label for="article-category" class="{{ $label }}">Primary Category</label>
                                <span class="relative block">
                                    <select id="article-category" name="category" class="{{ $input }} appearance-none pr-[40px] font-medium">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}" @selected(old('category', $article->category) === $category)>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                    <img src="{{ $icon('field-chevron.svg') }}" alt="" class="pointer-events-none absolute right-[12px] top-1/2 h-[5.6px] w-[9px] -translate-y-1/2">
                                </span>
                            </div>
                            <div class="flex flex-col gap-[8px]">
                                <label for="article-segment" class="{{ $label }}">Target Reader Segment</label>
                                <span class="relative block">
                                    <select id="article-segment" name="reader_segment" class="{{ $input }} appearance-none pr-[40px] font-medium">
                                        <option value="">Select segment…</option>
                                        @foreach ($segments as $segment)
                                            <option value="{{ $segment }}" @selected(old('reader_segment', $article->reader_segment) === $segment)>{{ $segment }}</option>
                                        @endforeach
                                    </select>
                                    <img src="{{ $icon('field-chevron.svg') }}" alt="" class="pointer-events-none absolute right-[12px] top-1/2 h-[5.6px] w-[9px] -translate-y-1/2">
                                </span>
                            </div>
                        </div>

                        <div class="grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-2">
                            {{-- AUTHOR bar (Figma): half-width under the category row; "Add new author…" reveals name / role fields --}}
                        @php $currentAuthor = old('author_id', $article->author_id ?? ($authors->firstWhere('name', $article->author_name)?->id)); @endphp
                        <div class="flex flex-col gap-[6px]" data-author>
                            <label for="article-author-id" class="{{ $label }}">Author</label>
                            <span class="relative block">
                                <select id="article-author-id" name="author_id" class="{{ $input }} appearance-none pr-[40px] font-medium" data-author-select>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}" @selected((string) $currentAuthor === (string) $author->id)>{{ $author->signature }}</option>
                                    @endforeach
                                    <option value="new" @selected($currentAuthor === 'new' || (blank($currentAuthor) && $authors->isEmpty()))>+ Add new author…</option>
                                </select>
                                <img src="{{ $icon('field-chevron.svg') }}" alt="" class="pointer-events-none absolute right-[12px] top-1/2 h-[5.6px] w-[9px] -translate-y-1/2">
                            </span>
                            @error('author_id') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror

                            {{-- Shown when "+ Add new author…" is picked; the same fields update the
                                 chosen author otherwise, and they fill the byline and author card. --}}
                            @php $selected = $authors->firstWhere('id', (int) $currentAuthor); @endphp

                            <div class="mt-[4px] grid [&>*]:min-w-0 gap-[10px] rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] p-[10px]" data-author-new @if ($currentAuthor !== 'new' && ! ($authors->isEmpty() && blank($currentAuthor))) hidden @endif>
                                <div class="flex flex-col gap-[4px]">
                                    <label for="article-author" class="font-jakarta text-[11px] font-semibold text-editorial-body">Author Name <span class="text-[#ba1a1a]">*</span></label>
                                    <input id="article-author" name="author_name" value="{{ old('author_name') }}" placeholder="Capt. Wayan Sudira" class="{{ $input }} bg-surface py-[9px] text-[12px] leading-[16px]">
                                    @error('author_name') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Author profile: what the reader sees under the article. --}}
                            <div class="mt-[8px] grid [&>*]:min-w-0 gap-[10px] rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] p-[10px]">
                                <div class="flex items-center gap-[12px]">
                                    <span class="flex size-[48px] shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#d5e2e9] font-jakarta text-[15px] font-bold text-[#58646a]">
                                        @if ($selected?->photo_url)
                                            <img src="{{ $selected->photo_url }}" alt="" class="size-full object-cover">
                                        @else
                                            {{ $selected?->initials ?? '—' }}
                                        @endif
                                    </span>

                                    <label class="flex-1">
                                        <span class="block pb-[4px] font-jakarta text-[11px] font-semibold text-editorial-body">Profile Photo</span>
                                        <input type="file" name="author_photo" accept="image/*"
                                               class="w-full font-jakarta text-[12px] text-editorial-body file:mr-[8px] file:rounded-[6px] file:border-0 file:bg-[#e5eaee] file:px-[10px] file:py-[5px] file:font-jakarta file:text-[12px] file:text-editorial-ink">
                                        @error('author_photo') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                                    </label>
                                </div>

                                <div class="flex flex-col gap-[4px]">
                                    <label for="article-author-role" class="font-jakarta text-[11px] font-semibold text-editorial-body">Author Role / Title</label>
                                    <input id="article-author-role" name="author_role" value="{{ old('author_role', $selected?->role) }}" placeholder="Master Mariner"
                                           class="{{ $input }} bg-surface py-[9px] text-[12px] leading-[16px]">
                                </div>

                                <div class="flex flex-col gap-[4px]">
                                    <label for="article-author-credential" class="font-jakarta text-[11px] font-semibold text-editorial-body">Credential Badge</label>
                                    <input id="article-author-credential" name="author_credential" value="{{ old('author_credential', $selected?->credential) }}" placeholder="ANT-IV Certified"
                                           class="{{ $input }} bg-surface py-[9px] text-[12px] leading-[16px]">
                                </div>

                                <div class="flex flex-col gap-[4px]">
                                    <label for="article-author-bio" class="font-jakarta text-[11px] font-semibold text-editorial-body">Short Bio</label>
                                    <textarea id="article-author-bio" name="author_bio" rows="4" placeholder="A sentence or two about the author, shown in the card under the article."
                                              class="{{ $input }} resize-y bg-surface py-[9px] text-[12px] leading-[16px]">{{ old('author_bio', $selected?->bio) }}</textarea>
                                    <span class="font-jakarta text-[11px] leading-[16px] text-editorial-body">Leave empty to hide the author card.</span>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </section>

                {{-- 2. Rich Text Content Editor (1:8147): toolbar strip mirrors the design; paragraphs are stored as plain text --}}
                <section class="{{ $card }} overflow-hidden" data-body-editor data-editor>
                    @php
                        $rte = 'flex size-[32px] items-center justify-center rounded-[6px] text-editorial-ink transition-colors hover:bg-[#e5eaee] aria-pressed:bg-editorial/15 aria-pressed:text-editorial';
                        $stroke = 'size-[17px]';
                    @endphp

                    {{-- Toolbar: undo/redo, a block-format picker, then the inline marks. --}}
                    <div class="flex flex-wrap items-center gap-[4px] border-b border-[#ebeef0] bg-[#f1f4f6] px-[16px] pb-[9px] pt-[8px]">
                        <button type="button" data-editor-command="undo" title="Undo" aria-label="Undo" class="{{ $rte }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="{{ $stroke }}" aria-hidden="true">
                                <path d="M9 14 4 9l5-5"/><path d="M4 9h11a5 5 0 0 1 0 10h-3"/>
                            </svg>
                        </button>
                        <button type="button" data-editor-command="redo" title="Redo" aria-label="Redo" class="{{ $rte }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="{{ $stroke }}" aria-hidden="true">
                                <path d="m15 14 5-5-5-5"/><path d="M20 9H9a5 5 0 0 0 0 10h3"/>
                            </svg>
                        </button>

                        <span class="mx-[6px] h-[20px] w-px bg-[#d7dde2]"></span>

                        {{-- Pressing an active heading again turns the line back into a paragraph. --}}
                        <button type="button" data-editor-command="formatBlock" data-editor-value="h2" title="Heading 2"
                                class="{{ $rte }} w-[38px] font-jakarta text-[13px] font-bold tracking-[-0.3px]">H2</button>
                        <button type="button" data-editor-command="formatBlock" data-editor-value="h3" title="Heading 3"
                                class="{{ $rte }} w-[38px] font-jakarta text-[13px] font-bold tracking-[-0.3px]">H3</button>
                        <button type="button" data-editor-dropcap title="Drop cap (large first letter)" aria-label="Drop cap"
                                class="{{ $rte }} w-[38px] font-serif text-[19px] font-bold leading-none">T<span class="align-super text-[10px]">a</span></button>

                        <span class="mx-[6px] h-[20px] w-px bg-[#d7dde2]"></span>

                        <button type="button" data-editor-command="bold" title="Bold" aria-label="Bold" class="{{ $rte }} font-jakarta text-[16px] font-bold">B</button>
                        <button type="button" data-editor-command="italic" title="Italic" aria-label="Italic" class="{{ $rte }} font-jakarta text-[16px] italic">I</button>
                        <button type="button" data-editor-command="underline" title="Underline" aria-label="Underline" class="{{ $rte }} font-jakarta text-[16px] underline">U</button>

                        <span class="mx-[6px] h-[20px] w-px bg-[#d7dde2]"></span>

                        <button type="button" data-editor-command="insertUnorderedList" title="Bulleted list" aria-label="Bulleted list" class="{{ $rte }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="{{ $stroke }}" aria-hidden="true">
                                <path d="M9 6h11M9 12h11M9 18h11"/><circle cx="4.5" cy="6" r="1.2" fill="currentColor" stroke="none"/><circle cx="4.5" cy="12" r="1.2" fill="currentColor" stroke="none"/><circle cx="4.5" cy="18" r="1.2" fill="currentColor" stroke="none"/>
                            </svg>
                        </button>
                        <button type="button" data-editor-command="insertOrderedList" title="Numbered list" aria-label="Numbered list" class="{{ $rte }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="{{ $stroke }}" aria-hidden="true">
                                <path d="M10 6h10M10 12h10M10 18h10"/>
                                <text x="2" y="8" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">1</text>
                                <text x="2" y="14.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">2</text>
                                <text x="2" y="21" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">3</text>
                            </svg>
                        </button>
                        <button type="button" data-editor-command="formatBlock" data-editor-value="blockquote" title="Quote" aria-label="Quote" class="{{ $rte }}">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="{{ $stroke }}" aria-hidden="true">
                                <path d="M9.5 6.5c-2.6.7-4.5 3-4.5 5.8V17.5h5.5V12H7.8c0-1.7 1-2.9 2.4-3.3l-.7-2.2Zm9 0c-2.6.7-4.5 3-4.5 5.8V17.5h5.5V12h-2.7c0-1.7 1-2.9 2.4-3.3l-.7-2.2Z"/>
                            </svg>
                        </button>

                        <span class="mx-[6px] h-[20px] w-px bg-[#d7dde2]"></span>

                        <button type="button" data-editor-command="createLink" title="Insert link" aria-label="Insert link" class="{{ $rte }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="{{ $stroke }}" aria-hidden="true">
                                <path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1-1"/>
                            </svg>
                        </button>
                        <button type="button" data-editor-image title="Insert image" aria-label="Insert image" class="{{ $rte }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="{{ $stroke }}" aria-hidden="true">
                                <rect x="3" y="4" width="14" height="14" rx="2"/><path d="m3 14 4-4 5 5"/><circle cx="13" cy="9" r="1.3"/><path d="M19 15v6M22 18h-6"/>
                            </svg>
                        </button>

                        <span class="ml-auto font-jakarta text-[12px] font-medium leading-[16px] text-editorial-body">Word Count: <span data-word-count class="font-bold text-editorial-ink">0</span></span>
                    </div>

                    <input type="file" accept="image/*" class="sr-only" data-editor-image-input
                           data-endpoint="{{ route('admin.articles.image') }}">

                    <div class="px-[33px] py-[28px]">
                        <label for="article-body" class="sr-only">Article Body</label>
                        {{-- The contenteditable is what the admin types in; it mirrors into the
                             textarea that actually posts, so the field still works without JavaScript. --}}
                        <div data-editor-surface contenteditable="true" role="textbox" aria-multiline="true" aria-label="Article body" hidden
                             class="rich-text min-h-[420px] w-full font-jakarta text-[16px] leading-[28px] text-editorial-ink focus:outline-none">{!! \App\Support\RichText::clean(old('body', $article->body), \App\Support\RichText::ALLOWED_ARTICLE) !!}</div>

                        <textarea id="article-body" name="body" rows="18" required data-editor-input placeholder="The Badung Strait has long held legendary status among Indonesian seafarers…"
                                  class="w-full resize-y bg-transparent font-jakarta text-[16px] leading-[28px] text-editorial-ink placeholder:text-editorial-meta focus:outline-none">{{ old('body', $article->body) }}</textarea>
                        @error('body') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                    </div>
                </section>

                {{-- 3. Featured Hero Image (1:8275) --}}
                <section class="{{ $card }} p-[33px]" data-cover-picker>
                    <div class="{{ $head }}">
                        <span class="{{ $tile }}"><img src="{{ $icon('editor-hero.svg') }}" alt="" class="size-[15px]"></span>
                        <h2 class="{{ $h2 }}">Featured Hero Image</h2>
                    </div>

                    <div class="mt-[24px] flex flex-col gap-[16px]">
                        <label class="group relative block cursor-pointer overflow-hidden rounded-[12px] border border-[#c0c7d3] bg-[#f1f4f6] p-px">
                            <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" class="sr-only">
                            <img data-cover-preview src="{{ $article->image ? \App\Support\ImagePath::url($article->image, 'articles') : '' }}" alt="" @if (! $article->image) hidden @endif class="h-[288px] w-full rounded-[11px] object-cover">
                            @if (! $article->image)
                                <span data-cover-empty class="flex h-[288px] items-center justify-center font-jakarta text-[14px] text-editorial-body">Click to choose the hero image (JPG, PNG, WEBP up to 4 MB)</span>
                            @endif
                            {{-- Overlay actions (1:8285) appear on hover --}}
                            <span class="absolute inset-0 flex items-center justify-center gap-[12px] bg-[rgba(45,49,51,0.4)] opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <span class="flex items-center gap-[6px] rounded-[8px] bg-surface px-[16px] py-[8px] font-jakarta text-[12px] font-bold text-editorial-ink shadow-[0_10px_15px_-3px_rgba(0,0,0,0.1)]">
                                    <img src="{{ $icon('hero-replace.svg') }}" alt="" class="size-[11.7px]">
                                    Replace Photo
                                </span>
                            </span>
                            <span class="absolute bottom-[12px] left-[12px] flex items-center gap-[6px] rounded-[6px] bg-[rgba(45,49,51,0.8)] px-[12px] py-[4px] font-jakarta text-[12px] leading-[16px] text-[#eef1f3] backdrop-blur-[2px]">
                                <img src="{{ $icon('hero-info.svg') }}" alt="" class="size-[10px]">
                                <span data-cover-name>{{ $article->image ? 'Current hero image' : 'Recommended 1920 × 1080px (WebP or JPG)' }}</span>
                            </span>
                        </label>
                        @error('cover') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror

                        {{-- Caption & Alt Inputs (1:8303) --}}
                        <div class="grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-2">
                            <div class="flex flex-col gap-[8px]">
                                <label for="article-caption" class="{{ $label }}">Image Caption</label>
                                <input id="article-caption" name="hero_caption" value="{{ old('hero_caption', $article->hero_caption) }}" placeholder="En route to Nusa Penida: Sanjaya Express III crossing Badung Strait"
                                       class="{{ $input }} text-[12px] leading-[16px]">
                            </div>
                            <div class="flex flex-col gap-[8px]">
                                <label for="article-alt" class="{{ $label }}">Descriptive Alt Text (Accessibility &amp; SEO)</label>
                                <input id="article-alt" name="hero_alt" value="{{ old('hero_alt', $article->hero_alt) }}" placeholder="Sanjaya Express III fastboat sailing towards Nusa Penida limestone cliffs"
                                       class="{{ $input }} text-[12px] leading-[16px]">
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="flex flex-col gap-[24px]">
                {{-- 1. Publishing Settings (1:8317) --}}
                <section class="{{ $card }} p-[25px]">
                    <div class="flex items-center gap-[8px] border-b border-[#ebeef0] pb-[13px]">
                        <img src="{{ $icon('side-publish.svg') }}" alt="" class="size-[13.3px]">
                        <h2 class="{{ $sideH }}">Publishing Settings</h2>
                    </div>

                    <div class="mt-[16px] flex flex-col gap-[16px]">
                        <x-admin.radio-cards name="status" :options="$publishModes" :selected="$article->status?->value" />
                        {{-- Keywords stay on the record even though the design has no keyword field --}}
                        <input type="hidden" name="meta_keywords" value="{{ $keywords }}">

                    </div>
                </section>

                {{-- 2. SEO Optimization (1:8378) --}}
                <section class="{{ $card }} p-[25px]" data-seo>
                    <div class="flex items-center justify-between gap-[8px] border-b border-[#ebeef0] pb-[13px]">
                        <span class="flex items-center gap-[8px]">
                            <img src="{{ $icon('side-seo.svg') }}" alt="" class="size-[13.3px]">
                            <h2 class="{{ $sideH }}">SEO Optimization</h2>
                        </span>
                        <span class="shrink-0 rounded-full bg-[#d1fae5] px-[10px] py-[2px] font-jakarta text-[11px] font-semibold leading-[16px] text-[#065f46]">Score: <span data-seo-score>0</span>/100</span>
                    </div>

                    <div class="mt-[16px] flex flex-col gap-[16px]">
                        {{-- The address always follows the title, so there is nothing to type here
                             and the link on a card can never point at a different slug. --}}
                        <div class="flex flex-col gap-[6px]">
                            <span class="{{ $sideLabel }}">URL Permalink</span>
                            <span class="flex items-center overflow-hidden rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc]">
                                <span class="border-r border-[#c0c7d3] bg-[#f1f4f6] px-[10px] py-[9px] font-jakarta text-[12px] text-editorial-body">/artikel/</span>
                                <span data-permalink class="truncate px-[10px] py-[9px] font-jakarta text-[12px] font-medium leading-[16px] text-editorial-ink">{{ $article->slug ?: 'follows-the-article-title' }}</span>
                            </span>
                            <span class="font-jakarta text-[11px] leading-[16px] text-editorial-body">Generated from the title.</span>
                        </div>

                        <div class="flex flex-col gap-[6px]">
                            <span class="flex items-center justify-between">
                                <label for="article-meta-title" class="{{ $sideLabel }}">Meta Title</label>
                                <span class="font-jakarta text-[11px] text-editorial-body"><span data-count-for="meta_title">0</span>/60 chars</span>
                            </span>
                            <input id="article-meta-title" name="meta_title" value="{{ old('meta_title', $article->meta_title) }}" maxlength="70" placeholder="Nusa Penida Fast Boat Transfers Guide | Penida Gili"
                                   class="{{ $input }} bg-surface py-[9px] text-[12px] leading-[16px]">
                        </div>

                        <div class="flex flex-col gap-[6px]">
                            <span class="flex items-center justify-between">
                                <label for="article-meta-description" class="{{ $sideLabel }}">Meta Description</label>
                                <span class="font-jakarta text-[11px] text-editorial-body"><span data-count-for="meta_description">0</span>/160 chars</span>
                            </span>
                            <textarea id="article-meta-description" name="meta_description" rows="4" maxlength="160" placeholder="Planning a boat trip to Nusa Penida? Read official harbor reviews, departure timetables, luggage rules, and captain tips."
                                      class="{{ $input }} resize-y bg-surface py-[9px] text-[12px] leading-[16px]">{{ old('meta_description', $article->meta_description) }}</textarea>
                        </div>

                        {{-- Google SERP Snippet Preview (1:8415) --}}
                        <div class="flex flex-col gap-[8px] border-t border-[#ebeef0] pt-[16px]">
                            <span class="{{ $sideLabel }}">Live Google SERP Preview</span>
                            <div class="rounded-[8px] border border-[#ebeef0] bg-surface p-[13px]">
                                <span class="flex items-center gap-[8px]">
                                    <span class="flex size-[24px] items-center justify-center rounded-full bg-[#f1f4f6] font-jakarta text-[12px] font-bold text-editorial-ink">{{ mb_strtoupper(mb_substr($siteHost, 0, 1)) }}</span>
                                    <span class="truncate font-jakarta text-[11px] leading-[16px] text-editorial-body">{{ $siteHost }} › artikel › <span data-serp-slug>{{ \Illuminate\Support\Str::limit($article->slug ?: 'your-article', 18) }}</span></span>
                                </span>
                                <span data-serp-title class="mt-[4px] block font-jakarta text-[14px] font-medium leading-[20px] text-[#1a0dab]">{{ old('meta_title', $article->meta_title) ?: (old('title', $article->title) ?: 'Article title') }}</span>
                                <span data-serp-description class="mt-[2px] line-clamp-3 font-jakarta text-[12px] leading-[16px] text-editorial-body">{{ old('meta_description', $article->meta_description) ?: (old('excerpt', $article->excerpt) ?: 'Meta description preview appears here.') }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 3. Tags & Taxonomy (1:8428) --}}
                <section class="{{ $card }} p-[25px]">
                    <div class="flex items-center gap-[8px] border-b border-[#ebeef0] pb-[13px]">
                        <img src="{{ $icon('side-tags.svg') }}" alt="" class="size-[13.3px]">
                        <h2 class="{{ $sideH }}">Tags &amp; Taxonomy</h2>
                    </div>
                    <div class="mt-[16px] flex flex-col gap-[8px]" data-keywords data-chip-class="bg-[#d5e2e9] text-[#58646a]">
                        <input type="hidden" name="tags" value="{{ $tags }}">
                        <div class="flex flex-wrap items-center gap-[6px]">
                            <span class="contents" data-keywords-list></span>
                        </div>
                        <span class="relative block">
                            <input type="text" placeholder="Type tag and hit Enter..." autocomplete="off"
                                   class="{{ $input }} bg-surface py-[9px] pr-[36px] text-[12px] leading-[16px]">
                            <img src="{{ $icon('tag-add.svg') }}" alt="" class="pointer-events-none absolute right-[12px] top-1/2 size-[11.7px] -translate-y-1/2">
                        </span>
                        @error('tags') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                    </div>
                </section>

            </aside>
        </form>
    </x-admin.form-page>
@endsection
