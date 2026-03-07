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

        /* 下拉菜单 */
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

        /* 弹窗 */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 50;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .modal.show {
            display: flex;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .modal-content {
            position: relative;
            z-index: 2;
            background: #FEFCF7;
            border-radius: 16px;
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid #EDE5D8;
        }

        /* 文章列表项 */
        .post-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        .post-item:hover {
            border-left-color: #C6A961;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        /* 分页美化（兼容 Laravel 默认分页） */
        .pagination {
            @apply flex items-center justify-center space-x-1 mt-6;
        }
        .pagination a,
        .pagination span {
            @apply px-3 py-1.5 text-sm rounded-lg transition;
        }
        .pagination a {
            @apply text-brown-700 hover:bg-primary hover:text-white;
        }
        .pagination .active span {
            @apply bg-primary text-white;
        }
        .pagination .disabled span {
            @apply text-brown-300 cursor-not-allowed;
        }

        /* 返回顶部按钮初始隐藏 */
        #back-to-top {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, transform 0.3s;
        }
        #back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        #back-to-top:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="font-sans text-brown-800 flex flex-col min-h-screen">

    <!-- 导航栏 -->
    <nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-brown-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="text-xl font-bold text-brown-800 flex items-center gap-2">
                    <span class="text-primary">✧</span> 半刻
                </a>
                
                @auth
                    <div class="relative dropdown">
                        <div class="flex items-center space-x-2 cursor-pointer">
                            <span class="text-brown-800 hidden md:inline text-sm">欢迎, {{ Auth::user()->name }}</span>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-brown-800 flex items-center justify-center text-white font-medium text-sm shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>

                        <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-40 border border-brown-100">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">个人主页</a>
                            <a href="{{ route('my.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我的文章</a>
                            <form method="POST" action="{{ route('logout') }}" class="block px-4 py-1">
                                @csrf
                                <button type="submit" class="w-full text-left text-sm text-red-600 hover:bg-brown-50">登出</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <button onclick="showModal('login')" class="text-brown-800 hover:text-primary text-sm hidden md:block">登录</button>
                        <button onclick="showModal('register')" class="bg-primary text-white px-3 py-1.5 text-sm rounded-lg hover:bg-[#b89a55] transition">注册</button>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero 区域 -->
    <section class="hero py-12 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-20 h-20 rounded-full bg-white"></div>
            <div class="absolute bottom-10 right-10 w-32 h-32 rounded-full bg-white"></div>
        </div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <div class="inline-block mb-3 px-4 py-1 bg-white/10 rounded-full backdrop-blur-sm text-white/90 text-sm">
                ✦ 个人空间 ✦
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-3">个人主页</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">查看你的个人资料与创作记录</p>
        </div>
    </section>

    <!-- 主内容区 -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- 左侧：用户信息 + 文章列表 -->
            <div class="w-full lg:w-[65%]">
                @auth
                    <div class="bg-white rounded-2xl p-6 shadow-sm mb-8 border border-brown-100 relative overflow-hidden">
                        <!-- 装饰角标 -->
                        <div class="absolute top-0 right-0 w-20 h-20">
                            <div class="absolute top-0 right-0 border-t-[40px] border-r-[40px] border-t-primary/20 border-r-transparent"></div>
                        </div>

                        <!-- 用户头像与基本信息 -->
                        <div class="flex items-center gap-5 mb-6">
                            <div class="relative group">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                        alt="{{ $user->name }}" 
                                        class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-md group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-brown-800 flex items-center justify-center text-white font-bold text-2xl shadow-md group-hover:scale-105 transition-transform">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-400 rounded-full border-2 border-white"></div>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-brown-800 flex items-center gap-2">
                                    {{ $user->name }}
                                    <span class="text-xs bg-primary/10 text-primary px-2 py-0.5 rounded-full">认证作者</span>
                                </h2>
                                <p class="text-brown-600 flex items-center gap-1 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $user->email }}
                                </p>
                                <p class="text-sm text-brown-500 mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    加入于 {{ $user->created_at->format('Y年m月') }}
                                </p>
                            </div>
                        </div>

                        <!-- 数据统计（已移除 views 相关） -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-brown-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-primary">{{ $user->posts()->count() }}</div>
                                <div class="text-xs text-brown-600">文章总数</div>
                            </div>
                            <div class="bg-brown-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-primary">{{ $user->posts()->where('created_at', '>=', now()->subDays(30))->count() }}</div>
                                <div class="text-xs text-brown-600">近30天</div>
                            </div>
                        </div>

                        <!-- 最近文章 -->
                        <div class="pt-4 border-t border-brown-100">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-semibold text-brown-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                    最近文章
                                </h3>
                                <a href="{{ route('my.posts') }}" class="text-sm text-primary hover:underline flex items-center gap-1">
                                    查看全部 <span>→</span>
                                </a>
                            </div>

                            @if($posts->isEmpty())
                                <div class="text-center py-8 text-brown-500 bg-brown-50 rounded-lg">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-brown-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p>还没有发布文章</p>
                                    <a href="{{ route('home') }}" class="inline-block mt-3 text-primary hover:underline">开始写作 →</a>
                                </div>
                            @else
                                <ul class="space-y-2">
                                    @foreach($posts as $post)
                                        <li class="post-item bg-white">
                                            <a href="{{ route('posts.show', $post) }}" class="text-primary hover:text-[#b89a55] font-medium flex items-center gap-2">
                                                <span class="w-1 h-4 bg-primary rounded-full"></span>
                                                {{ Str::limit($post->title, 40) }}
                                            </a>
                                            <div class="flex items-center gap-3 mt-1 text-xs text-brown-500">
                                                <span>{{ $post->created_at->diffForHumans() }}</span>
                                                {{-- ✅ 已移除 views 显示 --}}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- 分页 -->
                                <div class="mt-6">
                                    {{ $posts->links() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 快捷操作 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-brown-100">
                        <h3 class="font-bold text-brown-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            快捷操作
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <a href="{{ route('home') }}" class="group inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-primary to-[#b89a55] text-white rounded-xl hover:shadow-lg transition-all hover:-translate-y-0.5 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                撰写新文章
                            </a>
                            <a href="{{ route('my.posts') }}" class="group inline-flex items-center justify-center gap-2 px-4 py-3 bg-white text-brown-700 border-2 border-brown-200 rounded-xl hover:border-primary hover:text-primary transition-all hover:-translate-y-0.5 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                管理文章
                            </a>
                        </div>
                        <div class="mt-4 pt-3 border-t border-brown-100">
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 text-sm text-brown-600 hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                ✏️ 编辑个人资料
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-brown-100">
                        <div class="w-24 h-24 mx-auto mb-4 bg-brown-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-brown-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-brown-800 mb-2">请先登录</h3>
                        <p class="text-brown-600 mb-6">登录后可查看个人主页与文章管理</p>
                        <button onclick="showModal('login')" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl hover:bg-[#b89a55] transition text-sm font-medium shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            立即登录
                        </button>
                    </div>
                @endauth
            </div>
        <!-- 右侧：侧边栏 -->
        <div class="w-full lg:w-[35%]">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24 border border-brown-100">
                <h2 class="text-xl font-bold text-brown-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    关于 半刻
                </h2>
                <p class="text-brown-600 text-sm leading-relaxed">
                    半刻，刚刚好。用 Markdown 写作，回归文字本身。
                </p>
                
                <div class="my-6 relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-brown-100"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-3 text-xs text-brown-400"> 半刻之间 </span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-sm text-brown-600 bg-brown-50 p-3 rounded-xl">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>半刻时光 · {{ now()->format('Y-m-d H:i') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-3 text-sm text-brown-600 bg-brown-50 p-3 rounded-xl">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>半刻之间 · 字字安心</span>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-brown-100">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-center p-2">
                            <div class="text-lg font-bold text-primary">Markdown</div>
                            <div class="text-xs text-brown-500">半刻写作</div>
                        </div>
                        <div class="text-center p-2">
                            <div class="text-lg font-bold text-primary">开源</div>
                            <div class="text-xs text-brown-500">半刻共享</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

        <footer 
        class="py-6 text-center text-sm mt-auto border-t" 
        style="background-color: #2D2B29; color: #f5f5f5; border-color: #6B5851;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-base font-medium mb-2 flex items-center justify-center gap-2">
            <span>✦</span> 半刻 <span>✦</span>
            </p>
            <p class="max-w-2xl mx-auto text-sm opacity-90" style="color: #d7cfc5;">
            人生海海，留半刻给文字。
            </p>
            <div class="mt-4 text-xs flex items-center justify-center gap-4 opacity-80" style="color: #c9c0b5;">
            <span>&copy;半刻 · 字里行间</span>
            <span>·</span>
            <span>MIT License</span>
            <span>·</span>
            <span>v1.0</span>
            </div>
        </div>

        <!-- 返回顶部按钮：文字用 #4B3832，背景保持白色 -->
        <button 
            id="back-to-top" 
            class="fixed bottom-6 right-6 w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-lg hover:bg-gray-100 opacity-0 invisible transition-all duration-300"
            style="color: #2D2B29;"
            aria-label="回到顶部">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </button>
        </footer>
    <!-- 登录/注册弹窗 -->
    <div id="login-modal" class="modal">
        <div class="overlay" onclick="hideModal()"></div>
        <div class="modal-content">
            <h3 class="text-2xl font-bold mb-6 text-center text-brown-800">登录到 半刻</h3>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-brown-800 mb-2 font-medium">邮箱地址</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-white" placeholder="you@example.com" required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-brown-800 mb-2 font-medium">密码</label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-white" placeholder="••••••••" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-[#b89a55] transition">登录</button>
            </form>
            <div class="mt-5 text-center">
                <button onclick="switchModal('register')" class="text-primary hover:underline">还没有账号？立即注册</button>
            </div>
        </div>
    </div>
    
    <div id="register-modal" class="modal">
        <div class="overlay" onclick="hideModal()"></div>
        <div class="modal-content">
            <h3 class="text-2xl font-bold mb-6 text-center text-brown-800">创建 半刻 账号</h3>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-brown-800 mb-2 font-medium">用户名</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-white" placeholder="yourname" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-brown-800 mb-2 font-medium">邮箱地址</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-white" placeholder="you@example.com" required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-brown-800 mb-2 font-medium">密码</label>
                    <input type="password" name="password" class="w-full px-4 py-3 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-white" placeholder="至少 8 位字符" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-brown-800 mb-2 font-medium">确认密码</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-3 border border-brown-100 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-white" placeholder="再次输入密码" required>
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-[#b89a55] transition">注册</button>
            </form>
            <div class="mt-5 text-center">
                <button onclick="switchModal('login')" class="text-primary hover:underline">已有账号？立即登录</button>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        function showModal(type) {
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
            document.getElementById(type + '-modal').classList.add('show');
        }
        function hideModal() {
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
        }
        window.showModal = showModal;
        window.hideModal = hideModal;
        window.switchModal = function(type) {
            hideModal();
            setTimeout(() => showModal(type), 150);
        };

        document.addEventListener('DOMContentLoaded', function () {
            const backToTopButton = document.getElementById('back-to-top');
            if (backToTopButton) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 400) {
                        backToTopButton.classList.add('visible');
                    } else {
                        backToTopButton.classList.remove('visible');
                    }
                });
                backToTopButton.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>
</body>
</html>