<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $fillable = ['title', 'slug'];

    public function posts(){
        return $this->hasMany('App\Models\Post', 'post_cat_id', 'id');
    }

    public static function getAllPostCategory(){
        return PostCategory::orderBy('title', 'ASC')->get();
    }

    public static function getBlogByCategory($slug){
        return PostCategory::with('posts')->where('slug', $slug)->first();
    }

    public static function countActivePostCategory(){
        return PostCategory::count();
    }

    // Scope để lấy categories có bài viết
    public function scopeWithPosts($query)
    {
        return $query->has('posts');
    }

    // Accessor để lấy số lượng bài viết
    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }
}
