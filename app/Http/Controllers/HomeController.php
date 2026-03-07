<?php

namespace App\Http\Controllers;

use Illuminate\View\View;


use App\Models\Post;


class HomeController
{
    public function index()
{
    $query = Post::query();

    if (!auth()->check()) {
        // 游客只能看公开文章
        $query->where('visibility', Post::VISIBILITY_PUBLIC);
    } else {
        // 登录用户：看公开 + 自己的私有
        $query->where(function ($q) {
            $q->where('visibility', Post::VISIBILITY_PUBLIC)
              ->orWhere('user_id', auth()->id());
        });
    }

    $posts = $query->with('user')->latest()->paginate(10);
    return view('home', compact('posts'));
}
}   
// class HomeController
// {

// // 如果是 HomeController
//       public function index()
//                 {
//                     $posts = Post::with('user')
//                         ->latest()
//                         ->get()
//                         ->map(function ($post) {
//                             return [
//                                 'id' => $post->id, // ←←← 新增这一行！
//                                 'title' => $post->title,
//                                 'excerpt' => $post->excerpt,
//                                 'date' => $post->created_at->format('Y-m-d'),
//                                 'author' => $post->user->name,
//                             ];
//                         });

//                     return view('home', compact('posts'));
//                 }
// }