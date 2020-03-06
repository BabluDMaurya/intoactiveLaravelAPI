<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Roll extends Model
{
   protected $guarded = ['id', 'name', 'user_type'];
}
