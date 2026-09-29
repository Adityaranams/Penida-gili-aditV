{{-- Figma node 1:9637 — admin Articles --}}
@extends('layouts.admin')

@section('title', 'Articles')

@section('admin-active', 'article')

@php $icon = fn ($file) => asset('images/icons/admin/article/'.$file); @endphp

@section('content')
    <x-admin.bulk-delete :action="route('admin.articles.bulk-destroy')" noun="articles" />

    <x-admin.listing
        heading="Articles"
        subtitle="Manage fastboat partner accommodations, island room inventories, and direct ticket packages."
        action="Add New Articles"
        :action-href="route('admin.articles.create')"
        :columns="['Article Details', 'Category', 'Author & Role', 'Views', 'Published Date', 'Status', 'Quick Actions']"
        :center-columns="['Category', 'Author & Role', 'Views', 'Published Date', 'Status', 'Quick Actions']"
        :paginator="$articles"
        entity="articles"
        :selectable="true">

        <x-slot:toolbar>
            @include('partials.admin.article-filters', [
                'action' => route('admin.articles'),
                'filters' => $filters,
                'authors' => $authors,
                'categories' => $categories,
                'bulkDelete' => true,
                {{-- Scheduled publishing is not offered in the editor, so it is not a filter either. --}}
                'statuses' => collect(\App\Enums\ArticleStatus::cases())
                    ->reject(fn ($s) => $s === \App\Enums\ArticleStatus::Scheduled)
                    ->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all(),
            ])
        </x-slot:toolbar>

        @forelse ($articles as $article)
            <tr class="border-b border-[rgba(192,199,211,0.2)] last:border-b-0">
                <x-admin.bulk-checkbox :value="$article->id" :label="$article->title" />

                {{-- Article details: 64×48 thumbnail, title, excerpt • read time (1:9738) --}}
                <td class="px-[24px] py-[16px]">
                    <span class="flex w-[320px] items-center gap-[16px]">
                        <img src="{{ $article->image_url }}" alt="" class="h-[48px] w-[64px] shrink-0 rounded-[8px] object-cover shadow-[0_0_0_1px_rgba(192,199,211,0.4),0_1px_2px_rgba(0,0,0,0.05)]">
                        <span class="flex min-w-0 flex-col gap-[2px]">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="truncate text-[14px] font-semibold leading-[20px] text-editorial-ink hover:text-editorial">{{ $article->title }}</a>
                            <span class="line-clamp-2 text-[12px] leading-[16px] text-[#525c6f]">{{ \Illuminate\Support\Str::limit($article->excerpt, 60) }} &bull; {{ $article->read_time_label }}</span>
                        </span>
                    </span>
                </td>

                {{-- Category pill (1:9746) --}}
                <td class="px-[16px] py-[16px] text-center">
                    <span class="inline-block whitespace-nowrap rounded-full bg-[#d2e4ff] px-[10px] py-[4px] text-[12px] font-semibold leading-[16px] text-[#001d37]">{{ $article->category }}</span>
                </td>

                {{-- Author & role with initials avatar (1:9748) --}}
                <td class="px-[16px] py-[16px] text-center">
                    <span class="inline-flex items-center gap-[10px] text-center">
                        <span class="flex size-[28px] shrink-0 items-center justify-center rounded-full bg-[rgba(0,94,161,0.1)] text-[12px] font-bold text-editorial">{{ $article->author_initials }}</span>
                        <span class="flex flex-col gap-[2px]">
                            <span class="whitespace-nowrap text-[12px] font-medium leading-[12px] text-editorial-ink">{{ $article->author_name }}</span>
                            <span class="whitespace-nowrap text-[11px] leading-[20px] text-[#525c6f]">{{ $article->author_role }}</span>
                        </span>
                    </span>
                </td>

                {{-- Views with trend glyph (1:9756) --}}
                <td class="px-[16px] py-[16px] text-center">
                    <span class="inline-flex items-center gap-[6px] whitespace-nowrap text-[14px] font-semibold leading-[20px] text-editorial-ink">
                        <img src="{{ $icon('row-views.svg') }}" alt="" class="size-[10.7px]">
                        {{ $article->views >= 1000 ? rtrim(rtrim(number_format($article->views / 1000, 1), '0'), '.').'K' : $article->views }}
                    </span>
                </td>

                <td class="whitespace-nowrap px-[16px] py-[16px] text-center text-[12px] leading-[16px] text-[#525c6f]">{{ $article->published_at?->format('d M Y') ?? '—' }}</td>

                <td class="px-[16px] py-[16px] text-center"><x-admin.status :label="$article->status->label()" :tone="$article->status->tone()" /></td>

                {{-- Edit · View · Delete (1:9767) --}}
                <td class="px-[24px] py-[16px]">
                    <span class="flex items-center justify-center gap-[4px]">
                        <a href="{{ route('admin.articles.edit', $article) }}" aria-label="Edit {{ $article->title }}"
                           class="flex size-[26px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6]">
                            <img src="{{ $icon('action-edit.svg') }}" alt="" class="size-[13.5px]">
                        </a>
                        <a href="{{ route('articles.show', $article) }}" target="_blank" rel="noopener" aria-label="View {{ $article->title }}"
                           class="flex size-[28px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6]">
                            <img src="{{ $icon('action-view.svg') }}" alt="" class="h-[11.25px] w-[16.5px]">
                        </a>
                        <form action="{{ route('admin.articles.destroy', $article) }}" method="post" onsubmit="return confirm('Delete {{ addslashes($article->title) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Delete {{ $article->title }}"
                                    class="flex size-[26px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#fee2e2]">
                                <img src="{{ $icon('action-delete.svg') }}" alt="" class="h-[13.5px] w-[12px]">
                            </button>
                        </form>
                    </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-[16px] py-[32px] text-center text-[15px] text-editorial-body">No articles match this filter.</td></tr>
        @endforelse
    </x-admin.listing>
@endsection
