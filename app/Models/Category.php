<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title', 'slug'];

    public function products(){
        return $this->hasMany('App\Models\Product', 'cat_id', 'id');
    }

    public static function getAllCategory(){
        return Category::orderBy('id', 'DESC')->get();
    }

    public static function getAllParentWithChild(){
        return Category::orderBy('title', 'ASC')->get();
    }

    public static function getProductByCat($slug){
        return Category::with('products')->where('slug', $slug)->first();
    }

    public static function countActiveCategory(){
        return Category::count();
    }

    // Scope để lấy categories có sản phẩm
    public function scopeWithProducts($query)
    {
        return $query->has('products');
    }

    // Accessor để lấy số lượng sản phẩm
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
