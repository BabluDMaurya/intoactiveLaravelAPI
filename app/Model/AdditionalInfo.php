<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdditionalInfo extends Model
{
    protected $fillable = [
        'user_id','tag_line','more_about_me','class_names_id'
    ];
}
