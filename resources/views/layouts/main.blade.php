<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '半刻' }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui'] },
                    colors: {
                        primary: '#C6A961',
                        brown: {
                            DEFAULT: '#4B3832',
                            50: '#F8F4ED',
                            100: '#EDE5D8',
                            800: '#2D2B29'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* === 复用你首页的所有关键样式 === */
        * { box-sizing: border-box; }
        html { overflow-x: hidden; }
        body {
            margin: 0;
            padding: 0;
            background-color: #F8F4ED;
            font-family: 'Inter', system-ui;
        }
        .post-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); }
        .dropdown-menu {
            opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s ease-in-out; overflow: hidden;
        }
        .dropdown:hover .dropdown-menu,
        .dropdown:focus-within .dropdown-menu {
            opacity: 1; visibility: visible; transform: translateY(0);
        }
        .max-w-7xl { max-width: 1280px; width: 100%; }
        .flex, .flex-col, .flex-row { min-width: 0; }
    </style>
</head>
<body class="font-sans text-brown-800 flex flex-col min-h-screen">

<!-- 导航栏 -->
<nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-brown-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <a href="/" class="text-xl font-bold text-brown-800 flex items-center gap-1">
                MyBlog
                <span class="text-xs bg-primary/20 text-primary px-2 py-0.5 rounded-full">真·巨幕</span>
            </a>
            
           @auth
            <div class="relative dropdown group">
                <div class="flex items-center space-x-2 cursor-pointer">
                    <span class="text-brown-800 hidden md:inline text-sm">欢迎, {{ Auth::user()->name }}</span>
                    <div class="w-8 h-8 rounded-full bg-brown-800 flex items-center justify-center text-white font-medium text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>

                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-40 border border-brown-100
                            opacity-0 invisible group-hover:opacity-100 group-hover:visible
                            transition-all duration-200 ease-in-out origin-top-right">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">个人主页</a>
                    <a href="{{ route('my.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我的文章</a>
                    <a href="{{ route('liked.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我点赞过的</a>
                    <a href="{{ route('favorite.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我的收藏</a>
                    <form method="POST" action="{{ route('logout') }}" class="block px-4 py-1 mt-1 border-t border-brown-100">
                        @csrf
                        <button type="submit" class="w-full text-left text-sm text-red-600 hover:bg-brown-50">登出</button>
                    </form>
                </div>
            </div>
        @endauth
        </div>
    </div>
</nav>

<!-- 主体内容 -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-brown-800 text-white py-6 text-center text-sm mt-auto">
    <div class="max-w-7xl mx-auto px-4">
        <p class="text-lg font-medium mb-2">人生海海，留半刻给文字。</p>
        <p class="text-brown-300 max-w-2xl mx-auto text-xs">潮起潮落，写下来就不会忘记  </p>
        <div class="mt-4 text-brown-500 text-xs">&copy; 半刻 · 字里行间</div>
    </div>
</footer>

</body>
</html>