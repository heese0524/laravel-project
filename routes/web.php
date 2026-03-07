<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Models\Post;


Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/', function () {
//     $posts = Post::with('user')->latest()->get();
//     return view('welcome', compact('posts'));
// })->name('home');

// 搜索
Route::get('/search', [PostController::class, 'search'])->name('search');

// 文章详情
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// 登录后功能
Route::middleware('auth')->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    Route::get('/my/posts', [PostController::class, 'myPosts'])->name('my.posts');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    // Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show')->middleware('auth');
     Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
     Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
     Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
    Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/posts/{post}/like', [App\Http\Controllers\Api\PostInteractionController::class, 'toggleLike']);
    Route::post('/posts/{post}/favorite', [App\Http\Controllers\Api\PostInteractionController::class, 'toggleFavorite']);
    Route::post('/posts/{post}/comment', [App\Http\Controllers\Api\PostInteractionController::class, 'addComment']);
}); 
     Route::middleware('auth')->group(function () {
     Route::get('/liked-posts', [PostController::class, 'likedPosts'])->name('liked.posts');
     Route:: get('/favorite-posts', [PostController::class, 'favoritePosts'])->name('favorite.posts');
    });

    // 如果你有用户资料页
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Route::get('/test', function () {
//     if (class_exists(\App\Http\Requests\Auth\LoginRequest::class)) {
//         return '✅ LoginRequest exists!';
//     } else {
//         return '❌ Class not found!';
//     }
// });
// Route::get('/', function () {
//     $posts = [['title' => '测试', 'excerpt' => '...', 'date' => '2026-01-30']];
//     return view('home', compact('posts'));
// })->name('home');
// Route::get('/test', function () {
//     dd(class_exists(\HomeController::class)); // 应该返回 true
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
