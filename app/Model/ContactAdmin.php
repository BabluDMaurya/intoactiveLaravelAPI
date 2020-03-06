<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ContactAdmin extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'query_type', 'subject', 'message'
    ];

    
}
