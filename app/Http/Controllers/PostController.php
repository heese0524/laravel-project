<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
// Markdown 解析（正确方式）
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
class PostController extends Controller
{
    /**
     * 发布新文章（存入数据库）
     */
   public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'visibility' => 'required|in:public,private',
        ]);

        // ✅ 使用 CommonMark 渲染 excerpt
        $environment = new Environment(['html_input' => 'strip']);
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);
        $html = $converter->convert($request->content)->getContent();
        $excerpt = Str::limit(strip_tags($html), 150);

        auth()->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $excerpt,
            'visibility' => $request->visibility,
        ]);

        return redirect()->route('home')->with('success', '文章发布成功！');
    }

    /**
     * 显示单篇文章
     */
         public function show(Post $post)
    {
        // 👇 权限检查：确保用户有权查看该文章
        if (!auth()->check()) {
            // 游客只能看公开文章
            if ($post->visibility !== Post::VISIBILITY_PUBLIC) {
                abort(404); // 隐藏私有文章，返回 404 更安全
            }
        } else {
            // 登录用户：可看公开文章 + 自己的私有文章
            if ($post->visibility !== Post::VISIBILITY_PUBLIC && $post->user_id !== auth()->id()) {
                abort(403); // 或 abort(404)，根据你的隐私策略
            }
        }

        // 增加阅读量（可选）
        $post->increment('views_count');

        // 👇 使用 League\CommonMark 安全解析 Markdown（Laravel 自带，无需额外安装）
        $environment = new Environment([
            'html_input' => 'strip',           // 禁止原始 HTML，防 XSS
            'allow_unsafe_links' => false,     // 禁止 javascript: 等危险链接
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new StrikethroughExtension()); // 支持 ~~删除线~~
        $environment->addExtension(new TableExtension());         // 支持表格

        $converter = new MarkdownConverter($environment);
        $htmlContent = $converter->convert($post->content)->getContent();

        // 生成摘要（用于 meta description）
        $excerpt = Str::limit(strip_tags($htmlContent), 150);

        return view('posts.show', compact('post', 'htmlContent', 'excerpt'));
    }
    /**
     * 搜索文章（使用数据库）
     */
        public function search(Request $request)
        {
            $queryKeyword = trim($request->input('q', ''));
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // 如果没有任何筛选条件，跳回首页
            if ($queryKeyword === '' && empty($startDate) && empty($endDate)) {
                return redirect()->route('home');
            }

            // 👇 构建基础查询：只查当前用户可见的文章
            $posts = Post::query();

            if (!auth()->check()) {
                // 游客只能看公开文章
                $posts->where('visibility', 'public');
            } else {
                // 登录用户：看公开 + 自己的私有
                $posts->where(function ($q) {
                    $q->where('visibility', 'public')
                    ->orWhere('user_id', auth()->id());
                });
            }

            // 关键词筛选
            if ($queryKeyword !== '') {
                $posts->where(function ($q) use ($queryKeyword) {
                    $q->where('title', 'like', "%{$queryKeyword}%")
                    ->orWhere('content', 'like', "%{$queryKeyword}%");
                });
            }

            // 日期范围筛选
            if (!empty($startDate) || !empty($endDate)) {
                $start = $startDate ? \DateTime::createFromFormat('Y-m-d', $startDate) : null;
                $end   = $endDate   ? \DateTime::createFromFormat('Y-m-d', $endDate)   : null;

                if ($start && $end) {
                    $posts->whereBetween('created_at', [
                        $start->format('Y-m-d 00:00:00'),
                        $end->format('Y-m-d 23:59:59')
                    ]);
                } elseif ($start) {
                    $posts->where('created_at', '>=', $start->format('Y-m-d 00:00:00'));
                } elseif ($end) {
                    $posts->where('created_at', '<=', $end->format('Y-m-d 23:59:59'));
                }
            }

            $posts = $posts->with('user')
                        ->latest()
                        ->paginate(10);

            return view('home', compact('posts'));
        }
            public function myPosts(): View
                {
                    $posts = auth()->user()
                        ->posts()
                        ->with('user') // 虽然 user 就是自己，但保持与 home 一致
                        ->latest()
                        ->paginate(10);

                    return view('posts.my-posts', compact('posts'));
                }
            public function destroy(Post $post)
            {
                // 检查是否是文章作者
                if ($post->user_id !== auth()->id()) {
                    abort(403, '你没有权限删除这篇文章');
                }
                
                $post->delete();
                return redirect()->route('my.posts')->with('success', '文章已删除');
            }
          public function likedPosts()
        {
            $posts = auth()->user()
                ->likedPosts()
                ->where(function ($q) {
                    $q->where('posts.visibility', Post::VISIBILITY_PUBLIC)
                    ->orWhere('posts.user_id', auth()->id());
                })
                ->with('user')
                ->latest()
                ->paginate(10);

            // 👇 同样预渲染摘要
            $environment = new Environment([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
            $environment->addExtension(new CommonMarkCoreExtension());
            $converter = new MarkdownConverter($environment);

            $posts->getCollection()->transform(function ($post) use ($converter) {
                $html = $converter->convert($post->content)->getContent();
                $post->excerpt = Str::limit(strip_tags($html), 120);
                return $post;
            });

            return view('posts.liked', compact('posts'));
        }

             public function favoritePosts()
                {
                    $posts = auth()->user()
                        ->favoritedPosts()
                        ->where(function ($q) {
                            $q->where('posts.visibility', Post::VISIBILITY_PUBLIC)
                            ->orWhere('posts.user_id', auth()->id());
                        })
                        ->with('user')
                        ->latest()
                        ->paginate(10);

                    // 👇 预渲染每篇的摘要（安全解析 Markdown）
                    $environment = new Environment([
                        'html_input' => 'strip',
                        'allow_unsafe_links' => false,
                    ]);
                    $environment->addExtension(new CommonMarkCoreExtension());
                    $converter = new MarkdownConverter($environment);

                    $posts->getCollection()->transform(function ($post) use ($converter) {
                        $html = $converter->convert($post->content)->getContent();
                        $post->excerpt = Str::limit(strip_tags($html), 120);
                        return $post;
                    });

                    return view('posts.favorites', compact('posts'));
                }
// public function myPosts(): View
// {
//     $posts = auth()->user()->posts()->paginate(5);
//     return view('tests', compact('posts'));
// }
}