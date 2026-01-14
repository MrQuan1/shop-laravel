<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'slug', 'summary', 'description', 'photo', 'stock',
        'condition', 'price', 'discount', 'is_featured', 'cat_id', 'brand_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'is_featured' => 'boolean',
        'stock' => 'integer'
    ];

    // Relationships
    public function cat_info(){
        return $this->belongsTo('App\Models\Category', 'cat_id', 'id');
    }

    public function brand(){
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id');
    }

    public function brand_info(){
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id');
    }

    public function rel_prods(){
        return $this->hasMany('App\Models\Product', 'cat_id', 'cat_id')
            ->where('stock', '>', 0)
            ->where('id', '!=', $this->id)
            ->orderBy('id', 'DESC')
            ->limit(8);
    }

    public function getReview(){
        return $this->hasMany('App\Models\ProductReview', 'product_id', 'id')
            ->with('user_info')
            ->orderBy('id', 'DESC');
    }

    public function carts(){
        return $this->hasMany('App\Models\Cart', 'product_id', 'id');
    }

    public function wishlists(){
        return $this->hasMany('App\Models\Wishlist', 'product_id', 'id');
    }

    // Static methods
    public static function getProductBySlug($slug){
        return Product::with(['cat_info', 'brand', 'rel_prods', 'getReview'])
            ->where('slug', $slug)
            ->first();
    }

    public static function countActiveProduct(){
        return Product::where('stock', '>', 0)->count();
    }

    public static function getAllProduct(){
        return Product::with(['cat_info', 'brand'])
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public static function getProductByCategory($cat_id){
        return Product::where('cat_id', $cat_id)
            ->where('stock', '>', 0)
            ->paginate(10);
    }

    public static function getProductByBrand($brand_id){
        return Product::where('brand_id', $brand_id)
            ->where('stock', '>', 0)
            ->paginate(10);
    }

    public static function sumActiveProduct(){
        return Product::where('stock', '>', 0)->sum('stock');
    }

    public static function getFeaturedProducts(){
        return Product::where('stock', '>', 0)
            ->where('is_featured', 1)
            ->limit(8)
            ->get();
    }

    public static function getProductsByCondition($condition){
        return Product::where('stock', '>', 0)
            ->where('condition', $condition)
            ->limit(8)
            ->get();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0) . 'đ';
    }

    public function getFinalPriceAttribute()
    {
        if ($this->discount > 0) {
            return $this->price - ($this->price * $this->discount / 100);
        }
        return $this->price;
    }

    public function getFormattedFinalPriceAttribute()
    {
        return number_format($this->final_price, 0) . 'đ';
    }
}
