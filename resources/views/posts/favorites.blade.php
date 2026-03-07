@extends('layouts.main')
@section('title', '我的收藏')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl md:text-3xl font-bold text-brown-800 mb-6">我的收藏</h1>

    @if($posts->count())
        <div class="space-y-5">
            @foreach($posts as $post)
                <article class="post-card bg-white rounded-xl p-6 shadow-sm border border-brown-100 transition hover:shadow-md">
                    <div class="text-xs text-primary font-medium mb-2 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-primary rounded-full"></span>
                        我的收藏
                    </div>
                    <h3 class="text-xl font-bold text-brown-800 mb-3 break-words">
                        <a href="{{ route('posts.show', $post) }}" class="hover:text-primary transition-colors">
                            {{ $post->title }}
                        </a>
                    </h3>
                    <p class="text-brown-600 mb-4 leading-relaxed break-words">
                        {{ $post->excerpt }}
                    </p>
                    <div class="flex justify-between items-center border-t border-brown-100 pt-3">
                        <span class="text-sm text-brown-500">{{ $post->created_at->format('Y-m-d') }}</span>
                        <a href="{{ route('posts.show', $post) }}" class="text-primary font-medium text-sm hover:underline flex items-center gap-1">
                            阅读全文
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-brown-400 mb-4">还没有点赞任何文章</div>
            <a href="{{ route('home') }}" class="inline-block px-4 py-2 bg-primary text-white rounded hover:bg-[#b89a55] transition">
                去首页看看
            </a>
        </div>
    @endif
</div>
@endsection