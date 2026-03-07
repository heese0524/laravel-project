@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brown-800">个人主页</h1>
        <p class="text-brown-600">用户名：{{ auth()->user()->name }}</p>
        <p class="text-brown-600">邮箱：{{ auth()->user()->email }}</p>
    </div>

    <h2 class="text-xl font-semibold text-brown-800 mb-4">我发布的文章</h2>
    @foreach(auth()->user()->posts()->latest()->take(5)->get() as $post)
        <div class="mb-3">
            <a href="{{ route('posts.show', $post) }}" class="text-primary hover:text-[#b89a55]">{{ $post->title }}</a>
        </div>
    @endforeach
    <a href="{{ route('my.posts') }}" class="text-sm text-primary hover:underline block mt-2">查看全部文章 →</a>
</div>
@endsection