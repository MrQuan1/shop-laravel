<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'summary', 'description', 'quote', 'photo',
        'post_cat_id', 'added_by'
    ];

    public function cat_info(){
        return $this->belongsTo('App\Models\PostCategory', 'post_cat_id', 'id');
    }

    public function author_info(){
        return $this->belongsTo('App\User', 'added_by', 'id');
    }

    public function comments(){
        return $this->hasMany('App\Models\PostComment', 'post_id', 'id');
    }

    public static function getPostBySlug($slug){
        return Post::with(['cat_info', 'author_info', 'comments'])
            ->where('slug', $slug)
            ->first();
    }

    public static function countActivePost(){
        return Post::count();
    }

    public static function getAllPost(){
        return Post::with(['cat_info', 'author_info'])
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public static function getPostByCategory($cat_id){
        return Post::where('post_cat_id', $cat_id)
            ->paginate(10);
    }

    // Accessors
    public function getExcerptAttribute()
    {
        return substr(strip_tags($this->summary), 0, 150) . '...';
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }
}
