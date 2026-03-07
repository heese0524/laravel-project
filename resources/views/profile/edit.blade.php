 @extends('layouts.main')
@section('content')
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑资料 - MyBlog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { /* 你的配色 */ }
    </script>
    <style>/* 你的样式 */</style>
</head>
<body class="font-sans text-brown-800">

<nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-brown-100">
    <!-- 导航栏（可复用 profile.blade.php 的） -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <a href="/" class="text-xl font-bold text-brown-800">半刻</a>
            <a href="{{ route('profile') }}" class="text-primary text-sm">← 返回个人主页</a>
        </div>
    </div>
</nav>

<section class="py-12">
    <div class="max-w-2xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-brown-800 mb-6">编辑个人资料</h1>

        @if(session('success'))
            <div class="mb-6 p-3 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- 头像预览 -->
            <div class="mb-6">
                <label class="block text-brown-800 font-medium mb-2">头像</label>
                <div class="flex items-center gap-4">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                             alt="头像" 
                             class="w-16 h-16 rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-brown-800 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <input type="file" name="avatar" accept="image/*" class="text-sm text-brown-600">
                    <p class="text-xs text-brown-500">支持 JPG/PNG，最大 2MB</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-brown-800 font-medium mb-2">用户名</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full px-4 py-2.5 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none" required>
            </div>

            <div class="mb-6">
                <label class="block text-brown-800 font-medium mb-2">邮箱</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full px-4 py-2.5 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none" required>
            </div>

            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-[#b89a55] transition font-medium">
                保存更改
            </button>
        </form>
    </div>
</section>

</body>
</html>
@endsection