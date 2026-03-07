<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>半刻 · 写给自己的独白</title>
    <!-- Tailwind CSS 官方稳定版 -->
     <script src="https://cdn.tailwindcss.com"></script>
    <script>
    // 只有 tailwind 存在时才配置
    if (typeof tailwind !== 'undefined') {
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
        };
    } else {
        console.warn('Tailwind failed to load');
        // 可选：降级样式
        document.body.classList.add('fallback-styles');
    }
</script>
    <!-- SimpleMDE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <style>
        /* 基础重置 */
        * {
            box-sizing: border-box;
        }
        html {
            overflow-x: hidden;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #F8F4ED;
            font-family: 'Inter', system-ui;
        }
        
        .hero { 
            background: linear-gradient(135deg, #4B3832 0%, #2D2B29 100%); 
            color: white; 
        }
        
        .post-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); 
        }

        /* 下拉菜单 */
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s ease-in-out;
        }
        .group:hover .dropdown-menu {
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

        /* 木鱼动画 */
        .muyu:active { 
            transform: scale(0.94); 
        }
        .muyu-active { 
            animation: gentle-shake 0.15s ease; 
        }
        @keyframes gentle-shake {
            0% { transform: rotate(0deg) scale(1); }
            25% { transform: rotate(4deg) scale(1.1); }
            75% { transform: rotate(-4deg) scale(1.05); }
            100% { transform: rotate(0deg) scale(1); }
        }

        /* 编辑器样式 */
        .editor-fixed .CodeMirror {
            min-height: 750px !important;
            height: auto !important;
            border-radius: 0.5rem;
            font-size: 15px !important;
        }
        .editor-fixed .CodeMirror-scroll {
            min-height: 750px !important;
            height: auto !important;
        }
        .editor-fixed .editor-toolbar {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            background: #f9f5ef;
            padding: 6px 10px !important;
        }

        /* 待办动画 */
        .todo-item {
            transition: opacity 0.2s, transform 0.2s;
        }
        .todo-item.completing {
            opacity: 0;
            transform: translateX(10px);
        }
    </style>
</head>
<body class="font-sans text-brown-800 flex flex-col min-h-screen">

<!-- 导航栏 -->
<nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-brown-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <a href="/" class="text-xl font-bold text-brown-800 flex items-center gap-1">
                半刻
                <span class="text-xs bg-primary/20 text-primary px-2 py-0.5 rounded-full">随手写</span>
            </a>
            
           @auth
            <div class="relative group">
                <div class="flex items-center space-x-2 cursor-pointer">
                    <span class="text-brown-800 hidden md:inline text-sm">欢迎, {{ Auth::user()->name }}</span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-brown-800 flex items-center justify-center text-white font-medium text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>

                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-40 border border-brown-100">
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
            @else
            <div class="flex items-center space-x-3">
                <button onclick="showModal('login')" class="text-brown-800 hover:text-primary text-sm hidden md:block">登录</button>
                <button onclick="showModal('register')" class="bg-primary text-white px-3 py-1.5 text-sm rounded-lg hover:bg-[#b89a55] transition">注册</button>
            </div>
            @endauth
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero py-6">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-2xl md:text-3xl font-bold mb-1">写下即所有</h1>
        <p class="text-base opacity-90 max-w-2xl mx-auto">每天半刻，写给自己的独白。</p>
    </div>
</section>

<!-- 主体 -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow w-full">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- 左侧窄栏 -->
        <div class="w-full lg:w-[18%] space-y-5 flex-shrink-0">
            <!-- 待办 -->
            <div class="bg-white rounded-xl shadow-sm p-5 border border-brown-100">
                <div class="flex justify-between items-center mb-3 border-b border-brown-100 pb-2">
                    <h2 class="text-base font-bold text-brown-800 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        今日待办
                    </h2>
                    <button id="complete-all-btn" class="text-xs text-primary border border-primary/30 px-2 py-1 rounded hover:bg-primary/10 transition">
                        全部完成
                    </button>
                </div>
                <ul id="todo-list" class="space-y-2 mb-4 text-base max-h-[250px] overflow-y-auto"></ul>
                <div class="relative">
                    <input type="text" id="new-todo" placeholder="写一个新任务，回车添加..." 
                        class="w-full text-sm px-3 py-2.5 pr-10 border border-brown-200 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary"
                        autocomplete="off">
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-brown-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 木鱼 -->
            <div class="bg-white rounded-xl shadow-sm p-5 border border-brown-100 text-center">
                <div class="flex flex-col items-center">
                    <div id="muyu-box" class="relative cursor-pointer select-none group">
                        <div class="absolute inset-0 bg-primary/5 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <svg id="muyu-svg" class="w-20 h-20 text-primary drop-shadow-md muyu transition-all duration-75 relative z-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                            <ellipse cx="12" cy="12" rx="8" ry="5" stroke="currentColor" fill="#EDE5D8"/>
                            <path d="M8 10 L16 10" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="12" cy="12" r="2" fill="#C6A961" stroke="none"/>
                            <path d="M12 5 L12 2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M10 3 L14 3" stroke="currentColor" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        <span class="text-sm text-brown-600">今日功德</span>
                        <span class="text-primary font-bold text-2xl" id="gongde-counter">0</span>
                    </div>
                    <div class="w-full bg-brown-100 rounded-full h-1.5 mt-2">
                        <div id="gongde-progress" class="bg-primary h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button id="knock-muyu" class="bg-primary/10 text-primary border border-primary/30 px-5 py-1.5 rounded-full text-sm hover:bg-primary/20 transition flex items-center gap-1">
                            <span>🔨</span> 敲一下
                        </button>
                        <button id="reset-gongde" class="text-sm text-brown-400 border border-brown-200 px-3 py-1.5 rounded-full hover:bg-brown-50 transition">
                            重置
                        </button>
                    </div>
                    <p id="gongde-quote" class="text-[10px] text-brown-400 mt-3 italic">敲木鱼，攒善念</p>
                </div>
            </div>
        </div>

        <!-- 右侧主区 -->
        <div class="w-full lg:w-[82%] flex flex-col lg:flex-row gap-6">
            <!-- 文章列表 -->
            <div class="w-full lg:w-[50%] space-y-5">
                <form action="{{ route('search') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-grow">
                        <input type="text" name="q" placeholder="搜索文章..." value="{{ request('q') }}" class="w-full px-4 py-2.5 pl-10 pr-4 text-sm border border-brown-100 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-brown-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="flex gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full sm:w-32 px-3 py-2.5 text-xs border border-brown-100 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary bg-white">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full sm:w-32 px-3 py-2.5 text-xs border border-brown-100 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary bg-white">
                    </div>
                    <button type="submit" class="sm:hidden bg-primary text-white px-4 py-2.5 text-sm rounded-lg hover:bg-[#b89a55] transition">搜索</button>
                </form>

                @if(request()->filled('q'))
                <div class="text-sm text-brown-600 bg-white rounded-lg p-3 border border-brown-100">
                    @if($posts->count() > 0)
                    共找到 <span class="font-bold text-primary">{{ $posts->total() }}</span> 篇包含 “<span class="text-brown-800">{{ e(request('q')) }}</span>” 的文章
                    @else
                    暂无包含 “<span class="text-brown-800">{{ e(request('q')) }}</span>” 的文章
                    @endif
                </div>
                @endif

                <div class="space-y-5">
                    @foreach($posts as $post)
                    <article class="post-card bg-white rounded-xl p-6 shadow-sm border border-brown-100 transition hover:shadow-md">
                        <div class="text-xs text-primary font-medium mb-2 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-primary rounded-full"></span>最新文章
                        </div>
                        <h3 class="text-xl font-bold text-brown-800 mb-3 break-words">{{ $post->title }}</h3>
                        <p class="text-brown-600 mb-4 leading-relaxed break-words">{{ $post->excerpt }}</p>
                        <div class="flex justify-between items-center border-t border-brown-100 pt-3">
                            <span class="text-sm text-brown-500">{{ $post->created_at->format('Y-m-d') }}</span>
                            <a href="{{ route('posts.show', $post->id) }}" class="text-primary font-medium text-sm hover:underline flex items-center gap-1">
                                阅读全文 
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </article>
                    @endforeach

                    @if($posts->isEmpty())
                    <div class="text-center py-12 text-brown-500 text-base bg-white rounded-xl border border-brown-100">
                        @if(request()->filled('q'))
                        没有找到 “{{ e(request('q')) }}”
                        @else
                        还没有文章，登录后开始写作吧！
                        @endif
                    </div>
                    @endif

                    @if(isset($posts) && method_exists($posts,'links'))
                    <div class="pt-6">{{ $posts->links() }}</div>
                    @endif
                </div>
            </div>

            <!-- 编辑器 -->
            <div class="w-full lg:w-[50%]">
                @auth
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-20 border border-brown-100 editor-fixed">
                    <h2 class="text-lg font-bold text-brown-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                        今日半刻
                    </h2>
                    
                    <div class="text-xs bg-brown-50 rounded-lg p-3 mb-4 flex justify-between text-brown-700">
                        <span>📅 <span id="current-date"></span></span>
                        <span>宜写作</span>
                        <span>📝 <span id="word-count">0</span> 字</span>
                    </div>

                    <div class="border border-brown-200 rounded-lg overflow-hidden bg-white">
                        <textarea id="markdown-editor">## 新文章&#10;&#10;在这里开始写作...</textarea>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-sm">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="visibility" value="public" checked class="sr-only peer">
                            <div class="w-5 h-5 rounded-full border-2 border-brown-300 peer-checked:border-primary flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-primary opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="ml-2 text-brown-800">公开</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="visibility" value="private" class="sr-only peer">
                            <div class="w-5 h-5 rounded-full border-2 border-brown-300 peer-checked:border-primary flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-primary opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="ml-2 text-brown-600">私密</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-5">
                        <button id="publish-btn" class="bg-primary text-white py-3 rounded text-sm font-medium hover:bg-[#b89a55] transition">发布</button>
                        <button id="save-draft-btn" class="bg-brown-100 text-brown-800 py-3 rounded text-sm font-medium hover:bg-brown-200 transition">草稿</button>
                        <button id="export-md-btn" class="bg-brown-800 text-white py-3 rounded text-sm font-medium hover:bg-black transition">导出</button>
                    </div>
                </div>
                @else
                <div class="bg-white rounded-xl shadow-sm p-8 sticky top-24 text-center border border-brown-100">
                    <h2 class="text-xl font-bold text-brown-800 mb-3">登录后开始写作</h2>
                    <p class="text-brown-600 text-sm mb-5">发布你的第一篇 Markdown 文章</p>
                    <button onclick="showModal('login')" class="w-full bg-primary text-white py-2.5 rounded-lg text-sm hover:bg-[#b89a55]">立即登录</button>
                </div>
                @endauth
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="bg-brown-800 text-white py-6 text-center text-sm mt-auto">
    <div class="max-w-7xl mx-auto px-4">
        <p class="text-lg font-medium mb-2">人生海海，留半刻给文字。</p>
        <p class="text-brown-300 max-w-2xl mx-auto text-xs">潮起潮落，写下来就不会忘记</p>
        <div class="mt-4 text-brown-500 text-xs">&copy; 2026 半刻 · 字里行间</div>
    </div>
    <button id="back-to-top" class="fixed bottom-6 right-6 w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center shadow-lg hover:bg-[#b89a55] opacity-0 invisible transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </button>
</footer>

<!-- 登录/注册弹窗 -->
<div id="login-modal" class="modal">
    <div class="overlay" onclick="hideModal()"></div>
    <div class="modal-content">
        <h3 class="text-2xl font-bold mb-6 text-center text-brown-800">登录到半刻</h3>
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
        <h3 class="text-2xl font-bold mb-6 text-center text-brown-800">创建半刻账号</h3>
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

<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script>
// 全局定义弹窗函数
window.showModal = function(type) {
    document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
    document.getElementById(type + '-modal')?.classList.add('show');
};

window.hideModal = function() {
    document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
};

window.switchModal = function(type) {
    hideModal();
    setTimeout(() => showModal(type), 150);
};

(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // === 编辑器初始化 ===
        const editorEl = document.getElementById('markdown-editor');
        let simpleMDE;
        
        if (editorEl) {
            simpleMDE = new SimpleMDE({
                element: editorEl,
                spellChecker: false,
                autofocus: false,
                toolbar: [
                    "bold", "italic", "heading", "|",
                    "quote", "code", "table", "|",
                    "unordered-list", "ordered-list", "|",
                    "link", "image", "|",
                    "preview"
                ],
                status: false,
                placeholder: "在这里开始写作..."
            });

            const savedDraft = localStorage.getItem('myblog_markdown_draft');
            if (savedDraft && savedDraft.trim()) {
                simpleMDE.value(savedDraft);
            }

            // 字数统计
            function countWords(content) {
                if (!content || !content.trim()) return 0;
                
                let cleanText = content
                    .replace(/```[\s\S]*?```/g, '')
                    .replace(/~~.*?~~/g, '')
                    .replace(/\*\*(.*?)\*\*/g, '$1')
                    .replace(/\*(.*?)\*/g, '$1')
                    .replace(/__(.*?)__/g, '$1')
                    .replace(/_(.*?)_/g, '$1')
                    .replace(/!\[.*?\]\(.*?\)/g, '')
                    .replace(/\[(.*?)\]\(.*?\)/g, '$1')
                    .replace(/^#{1,6}\s*/gm, '')
                    .replace(/^\s*[-*+]\s+/gm, '')
                    .replace(/^\s*\d+\.\s+/gm, '')
                    .replace(/^>\s+/gm, '')
                    .replace(/[`~]/g, '');

                if (!cleanText.trim()) return 0;
                return cleanText.replace(/\s+/g, '').length;
            }

            function updateWordCount() {
                if (!simpleMDE) return;
                const count = countWords(simpleMDE.value());
                document.getElementById('word-count').textContent = count;
            }

            simpleMDE.codemirror.on('change', updateWordCount);
            simpleMDE.codemirror.on('inputRead', updateWordCount);
            updateWordCount();
        }

        // === 按钮事件 ===
        document.getElementById('save-draft-btn')?.addEventListener('click', function() {
            if (!simpleMDE) return;
            const content = simpleMDE.value().trim();
            if (content) {
                localStorage.setItem('myblog_markdown_draft', content);
                alert('✅ 草稿已保存');
            } else {
                alert('⚠️ 内容为空');
            }
        });

        document.getElementById('export-md-btn')?.addEventListener('click', function() {
            if (!simpleMDE) return;
            const content = simpleMDE.value();
            if (!content.trim()) return alert('内容为空');
            
            const firstLine = content.split('\n')[0] || 'untitled';
            let filename = firstLine.replace(/[#*\[\]()`]/g, '').trim() || '半刻文章';
            filename = filename.substring(0, 30).replace(/\s+/g, '-') + '.md';
            
            const blob = new Blob([content], { type: 'text/markdown;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });

        document.getElementById('publish-btn')?.addEventListener('click', function() {
            const title = prompt('请输入文章标题：');
            if (!title || !title.trim()) return alert('标题不能为空');
            if (!simpleMDE) return alert('编辑器未加载');
            
            const content = simpleMDE.value().trim();
            if (content.length < 10) return alert('内容至少10个字符');

            const visibility = document.querySelector('input[name="visibility"]:checked')?.value || 'public';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) return alert('CSRF token 缺失');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('posts.store') }}";
            form.style.display = 'none';

            const addInput = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            };

            addInput('_token', csrfToken);
            addInput('title', title.trim());
            addInput('content', content);
            addInput('visibility', visibility);

            document.body.appendChild(form);
            form.submit();
            localStorage.removeItem('myblog_markdown_draft');
        });

        // === 待办 ===
        const todoList = document.getElementById('todo-list');
        const newTodo = document.getElementById('new-todo');
        const defaultTasks = ['优化博客首页布局', '添加写作日历', '发布第一篇正式文章'];
        
        function addTodoItem(text) {
            if (!text) return;
            const li = document.createElement('li');
            li.className = 'todo-item flex items-start gap-3 py-1';
            li.innerHTML = `<input type="checkbox" class="todo-checkbox mt-1 h-5 w-5 text-primary rounded focus:ring-primary shrink-0"><span class="text-brown-800 text-base leading-6 flex-1">${escapeHtml(text)}</span>`;
            
            li.querySelector('.todo-checkbox').addEventListener('change', function() {
                li.classList.add('completing');
                setTimeout(() => li.remove(), 200);
            });
            
            todoList.appendChild(li);
        }
        
        function escapeHtml(s) {
            return s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]);
        }
        
        defaultTasks.forEach(addTodoItem);
        
        newTodo?.addEventListener('keypress', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const text = e.target.value.trim();
                if (text) {
                    addTodoItem(text);
                    e.target.value = '';
                }
            }
        });
        
        document.getElementById('complete-all-btn')?.addEventListener('click', () => {
            document.querySelectorAll('#todo-list li').forEach(li => li.classList.add('completing'));
            setTimeout(() => todoList.innerHTML = '', 200);
        });

        // === 木鱼 ===
        let gongde = parseInt(localStorage.getItem('myblog_gongde') || '0');
        const counter = document.getElementById('gongde-counter');
        const progress = document.getElementById('gongde-progress');
        const quote = document.getElementById('gongde-quote');
        const quotes = ['功德 +1，心平气和', '敲走烦恼，敲来灵感', '今日代码无 bug', '文章阅读量 +100', '木鱼一响，黄金万两', '键盘敲烂，月入过万'];
        
        function updateGongde() {
            if (counter) counter.textContent = gongde;
            if (progress) progress.style.width = ((gongde % 10) / 10 * 100) + '%';
            if (quote) quote.textContent = quotes[Math.floor(Math.random() * quotes.length)];
            localStorage.setItem('myblog_gongde', gongde);
        }
        
        function knock() {
            gongde++;
            updateGongde();
            document.getElementById('muyu-svg')?.classList.add('muyu-active');
            setTimeout(() => document.getElementById('muyu-svg')?.classList.remove('muyu-active'), 150);
        }
        
        document.getElementById('knock-muyu')?.addEventListener('click', knock);
        document.getElementById('muyu-box')?.addEventListener('click', knock);
        document.getElementById('reset-gongde')?.addEventListener('click', e => {
            e.stopPropagation();
            if (confirm('清零功德？')) {
                gongde = 0;
                updateGongde();
            }
        });
        
        updateGongde();

        // === 日期 ===
        function updateDate() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            const weekdays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
            const dateSpan = document.getElementById('current-date');
            if (dateSpan) dateSpan.textContent = `${y}.${m}.${d} ${weekdays[now.getDay()]}`;
        }
        updateDate();

        // === 回到顶部 ===
        const topBtn = document.getElementById('back-to-top');
        if (topBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 400) {
                    topBtn.classList.remove('opacity-0', 'invisible');
                } else {
                    topBtn.classList.add('opacity-0', 'invisible');
                }
            });
            
            topBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
})();
</script>
</body>
</html>