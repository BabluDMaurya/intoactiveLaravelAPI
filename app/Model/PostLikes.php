<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostLikes extends Model
{
    //
     protected $fillable = ['user_id','post_id', 'is_delete'];
}
