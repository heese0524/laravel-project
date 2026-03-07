<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostInteractionController extends Controller
{
     public function toggleLike(Post $post): JsonResponse
    {
        // 👇 新增：检查当前用户是否有权操作这篇文章
        if (!$this->canAccessPost($post)) {
            return response()->json(['error' => '无权访问该文章'], 403);
        }

        $user = auth()->user();
        $post->likes()->toggle($user);

        return response()->json([
            'liked' => $post->likes()->where('user_id', $user->id)->exists(),
            'count' => $post->likes()->count(),
        ]);
    }

    public function toggleFavorite(Post $post): JsonResponse
{
    // 👇 新增：检查当前用户是否有权操作这篇文章
    if (!$this->canAccessPost($post)) {
        return response()->json(['error' => '无权访问该文章'], 403);
    }

    $user = auth()->user();
    $post->favorites()->toggle($user);

    return response()->json([
        'favorited' => $post->favorites()->where('user_id', $user->id)->exists(),
        'count' => $post->favorites()->count(),
    ]);
}
    private function canAccessPost(Post $post): bool
    {
        if (!auth()->check()) {
            return $post->visibility === Post::VISIBILITY_PUBLIC;
        }

        return $post->visibility === Post::VISIBILITY_PUBLIC || $post->user_id === auth()->id();
    }

    public function addComment(Post $post, Request $request): JsonResponse
    {
        $request->validate(['content' => 'required|string|max:1000']);

        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'comment' => [
                'content' => $comment->content,
                'user_name' => $comment->user?->name ?? '匿名',
                'user_initial' => strtoupper(substr($comment->user?->name ?? 'A', 0, 1)),
                'created_at' => $comment->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }
}