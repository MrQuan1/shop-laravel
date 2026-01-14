<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'name',
        'email',
        'phone',
        'address',
        'note',
    ];

    public function cart_info(){
        return $this->hasMany('App\Models\Cart', 'order_id', 'id');
    }

    public static function getAllOrder($id){
        return Order::with('cart_info')->find($id);
    }

    public static function countActiveOrder(){
        return Order::count();
    }

    public function cart(){
        return $this->hasMany('App\Models\Cart');
    }

    public function shipping(){
        return $this->belongsTo('App\Models\Shipping', 'shipping_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function countNewOrder(){
        return Order::where('status', 'new')->count();
    }

    public static function countProcessOrder(){
        return Order::where('status', 'process')->count();
    }

    public static function countDeliveredOrder(){
        return Order::where('status', 'delivered')->count();
    }

    public static function countCancelOrder(){
        return Order::where('status', 'cancel')->count();
    }

    public function orderDetails()
    {
        return $this->hasMany('App\Models\OrderDetail', 'order_id', 'id');
    }
}
