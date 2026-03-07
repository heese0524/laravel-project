<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $post->title . ' 半刻 ' }}</title>
    <meta name="description" content="{{ $excerpt }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui'] },
                    colors: {
                        primary: '#C6A961', // old-money gold
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
        .prose a { 
            color: #C6A961; 
            text-decoration: underline; 
        }
        .prose a:hover { 
            color: #b89a55; 
        }
        .post-actions button:hover svg { 
            transform: scale(1.1); 
        }
    </style>
</head>
<body class="font-sans text-brown-800 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- 导航栏：老钱风 -->
        <nav class="bg-white shadow-sm sticky top-0 z-30 border-b border-brown-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="/" class="text-xl font-bold text-brown-800">MyBlog</a>
                    
                    @auth
                        <!-- 👇 关键修改：添加 `group` 类，并控制下拉菜单显示 -->
                        <div class="relative dropdown group">
                            <div class="flex items-center space-x-2 cursor-pointer">
                                <span class="text-brown-800 hidden md:inline text-sm">欢迎, {{ Auth::user()->name }}</span>
                                <div class="w-8 h-8 rounded-full bg-brown-800 flex items-center justify-center text-white font-medium text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>

                            <!-- 👇 下拉菜单：默认隐藏，hover 时显示 + 动画 -->
                              <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-40 border border-brown-100
                                opacity-0 invisible group-hover:opacity-100 group-hover:visible
                                transition-all duration-200 ease-in-out origin-top-right">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">个人主页</a>
                            <a href="{{ route('my.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我的文章</a>
                            
                            <!-- 👇 新增：我点赞过的 -->
                            <a href="{{ route('liked.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我点赞过的</a>
                            
                            <!-- 👇 新增：我的收藏 -->
                            <a href="{{ route('favorite.posts') }}" class="block px-4 py-2 text-sm text-brown-800 hover:bg-brown-50">我的收藏</a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="block px-4 py-1 mt-1 border-t border-brown-100">
                                @csrf
                                <button type="submit" class="w-full text-left text-sm text-red-600 hover:bg-brown-50">登出</button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-3">
                            <a href="/" class="text-brown-800 hover:text-primary text-sm">返回首页</a>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- 文章卡片 -->
            <article class="bg-white rounded-xl shadow-sm p-6 border border-brown-100 mb-8">
                <header class="mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-brown-800 mb-3">{{ $post->title }}</h1>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-brown-500">
                        <span>作者：{{ $post->user->name ?? '匿名' }}</span>
                        <span>·</span>
                        <span>{{ $post->created_at->format('Y年m月d日 H:i') }}</span>
                        <span>·</span>
                        <span>{{ $post->views_count ?? 0 }} 次阅读</span>
                    </div>
                </header>

                <!-- 文章内容 -->
                <div class="prose prose-lg max-w-none prose-brown">
                    {!! $htmlContent !!}
                </div>
            </article>

            <!-- 互动区域 -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-brown-100 mb-8">
                <div class="post-actions flex flex-wrap items-center gap-4">
                   <!-- 点赞 -->
                    <button id="like-btn" class="flex items-center gap-2 {{ $post->likedByCurrentUser() ? 'text-red-600' : 'text-brown-600 hover:text-red-600' }} transition group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" fill="{{ $post->likedByCurrentUser() ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.215 1.416-.604 2.009L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        <span id="like-count">{{ $post->likeCount() }}</span>
                        <span>点赞</span>
                    </button>

                    <!-- 收藏 -->
                    <button id="favorite-btn" class="flex items-center gap-2 {{ $post->favoritedByCurrentUser() ? 'text-yellow-600' : 'text-brown-600 hover:text-yellow-600' }} transition group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" fill="{{ $post->favoritedByCurrentUser() ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span>收藏</span>
                    </button>
                </div>
            </div>

            <!-- 返回首页 -->
            <div class="mt-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-primary hover:text-[#b89a55] font-medium">
                    ← 返回首页
                </a>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-brown-800 text-white py-8 mt-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-brown-300 text-sm">
                &copy; 2026 MyBlog. MIT License.
            </p>
        </div>
    </footer>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const postId = {{ $post->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 点赞
    document.getElementById('like-btn')?.addEventListener('click', async () => {
        const res = await fetch(`/api/posts/${postId}/like`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await res.json();
        document.getElementById('like-count').textContent = data.count;
        const btn = document.getElementById('like-btn');
        const icon = btn.querySelector('svg');
        if (data.liked) {
            btn.classList.replace('text-brown-600', 'text-red-600');
            btn.classList.add('hover:text-red-600');
            icon.setAttribute('fill', 'currentColor');
        } else {
            btn.classList.replace('text-red-600', 'text-brown-600');
            btn.classList.remove('hover:text-red-600');
            icon.setAttribute('fill', 'none');
        }
    });

    // 收藏
    document.getElementById('favorite-btn')?.addEventListener('click', async () => {
        const res = await fetch(`/api/posts/${postId}/favorite`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await res.json();
        const btn = document.getElementById('favorite-btn');
        const icon = btn.querySelector('svg');
        if (data.favorited) {
            btn.classList.replace('text-brown-600', 'text-yellow-600');
            btn.classList.add('hover:text-yellow-600');
            icon.setAttribute('fill', 'currentColor');
        } else {
            btn.classList.replace('text-yellow-600', 'text-brown-600');
            btn.classList.remove('hover:text-yellow-600');
            icon.setAttribute('fill', 'none');
        }
    });

    // 评论（注意：你页面缺少评论输入框，建议后续加上）
    document.getElementById('submit-comment')?.addEventListener('click', async () => {
        const input = document.getElementById('comment-input');
        const content = input.value.trim();
        if (!content) {
            alert('请输入评论内容');
            return;
        }

        const res = await fetch(`/api/posts/${postId}/comment`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ content })
        });

        if (res.ok) {
            const data = await res.json();
            input.value = '';

            const commentHtml = `
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-brown-800 flex items-center justify-center text-white text-xs font-medium">
                        ${data.comment.user_initial}
                    </div>
                    <div class="flex-1">
                        <div class="bg-brown-50 rounded-lg p-3">
                            <p class="text-brown-800">${data.comment.content}</p>
                        </div>
                        <div class="text-xs text-brown-500 mt-1">${data.comment.created_at} · ${data.comment.user_name}</div>
                    </div>
                </div>
            `;

            const commentsList = document.getElementById('comments-list');
            if (commentsList.querySelector('.text-center')) {
                commentsList.innerHTML = '';
            }
            commentsList.insertAdjacentHTML('afterbegin', commentHtml);

            const titleEl = document.querySelector('h2.text-xl');
            if (titleEl) {
                const currentText = titleEl.textContent;
                const currentCount = parseInt(currentText.match(/\d+/)?.[0] || '0');
                titleEl.innerHTML = currentText.replace(/\d+/, currentCount + 1);
            }
        } else {
            alert('评论发布失败，请稍后重试');
        }
    });
});
</script>
</body>
</html>