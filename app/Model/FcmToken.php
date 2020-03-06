<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'user_id', 'token', 'apns_id'
    ];
}
