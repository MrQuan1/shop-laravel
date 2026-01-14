<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['title', 'slug'];

    public function products(){
        return $this->hasMany('App\Models\Product', 'brand_id', 'id');
    }

    public static function getBrandBySlug($slug){
        return Brand::where('slug', $slug)->first();
    }

    public static function countActiveBrand(){
        return Brand::count();
    }

    public static function getAllBrand(){
        return Brand::orderBy('title', 'ASC')->get();
    }

    // Thêm method để lấy sản phẩm theo brand với pagination
    public static function getProductByBrand($slug){
        return Brand::where('slug', $slug)->first();
    }

    // Scope để lấy brands có sản phẩm
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
