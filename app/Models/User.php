<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany; // ← 必须有这行！
use App\Models\Post; // ← 也要导入 Post

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ✅ 正确的关联方法
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
    // User.php
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user_likes', 'user_id', 'post_id');
    }

    public function favoritedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user_favorites', 'user_id', 'post_id');
    }
    
}