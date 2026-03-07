<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';
    protected $fillable = ['user_id', 'title', 'content', 'excerpt', 'visibility'];

    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // ✅ 修正：使用 belongsToMany 关联中间表
    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_user_likes', 'post_id', 'user_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'post_user_favorites', 'post_id', 'user_id');
    }

    // 辅助方法：当前用户是否点赞/收藏
        public function likedByCurrentUser()
        {
            return Auth::check() && $this->likes()->where('user_id', Auth::id())->exists();
        }

        public function favoritedByCurrentUser()
        {
            return Auth::check() && $this->favorites()->where('user_id', Auth::id())->exists();
        }

    // 计数（可选优化：用 withCount 提升性能）
    public function likeCount()
    {
        return $this->likes()->count();
    }

    public function favoriteCount()
    {
        return $this->favorites()->count();
    }
        // 判断是否对当前用户可见
    public function isVisibleToCurrentUser(): bool
    {
        if (auth()->check() && $this->user_id === auth()->id()) {
            return true; // 作者永远可见
        }
        return $this->visibility === self::VISIBILITY_PUBLIC;
    }

    // 判断是否允许互动（点赞/收藏/评论）
    public function isInteractable(): bool
    {
        return $this->visibility === self::VISIBILITY_PUBLIC;
    }
}