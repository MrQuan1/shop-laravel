<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = ['user_id', 'post_id', 'comment', 'parent_id'];

    public function user_info(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function replies(){
        return $this->hasMany('App\Models\PostComment', 'parent_id', 'id');
    }

    public function post() {
        return $this->belongsTo('App\Models\Post', 'post_id', 'id');
    }

    public static function getAllComments(){
        return PostComment::with('user_info')->orderBy('id', 'DESC')->get();
    }
}
