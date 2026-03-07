<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>半刻</title>
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
        body { background-color: #F8F4ED; font-family: 'Inter', system-ui; }
        .hero { background: linear-gradient(135deg, #4B3832 0%, #2D2B29 100%); color: white; }
        .post-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); }

        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s ease-in-out;
        }
        .dropdown:hover .dropdown-menu,
        .dropdown:focus-within .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    </style>
</head>
<body class="font-sans text-brown-800 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- 导航栏 -->
        <nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-brown-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="/" class="text-xl font-bold text-brown-800">半刻</a>
                    
                    @auth
                        <div class="relative dropdown">
                            <div class="flex items-center space-x-2 cursor-pointer">
                                <span class="text-brown-800 hidden md:inline text-sm">欢迎, {{ Auth::user()->name }}</span>
                                <div class="w-8 h-8 rounded-full bg-brown-800 flex items-center justify-center text-white font-medium text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>

                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-40 border border-brown-100">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">个人主页</a>
                                <a href="{{ route('my.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我的文章</a>
                                <form method="POST" action="{{ route('logout') }}" class="block px-4 py-1">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-brown-50 rounded-none">
                                        登出
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="/" class="text-brown-800 hover:text-primary">返回首页</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-brown-900 mb-6">我的文章</h1>

            @if(session('success'))
                <div class="mb-6 p-3 bg-green-100 text-green-800 rounded-lg text-sm border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if($posts->isEmpty())
                <div class="text-center py-12 text-brown-500">
                    你还没有发布任何文章。
                    <a href="/" class="text-primary hover:underline ml-2">去首页开始写作 →</a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($posts as $post)
                        <article class="post-card bg-white rounded-xl p-6 shadow-sm transition-all duration-300 border border-brown-100">
                            <div class="text-sm text-primary font-medium mb-2">技术笔记</div>
                            <h3 class="text-xl font-bold text-brown-800 mb-3">{{ $post->title }}</h3>
                            <p class="text-brown-600 mb-4 leading-relaxed">{{ $post->excerpt }}</p>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-brown-500">{{ $post->created_at->format('Y-m-d H:i') }}</span>
                                
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('posts.show', $post) }}" class="text-primary hover:text-[#b89a55] text-sm">阅读</a>
                                    
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('确定要删除这篇文章吗？此操作不可恢复。')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">
                                            删除
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <div class="mt-8">
                        {{ $posts->links() }}
                    </div>
                </div>
            @endif
        </main>
    </div>

    <footer class="bg-brown-800 text-white py-8 text-center text-sm mt-auto">
        &copy; 半刻. MIT License.
    </footer>
</body>
</html>