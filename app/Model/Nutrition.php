<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Nutrition extends Model
{
    //
     protected $fillable = [
        'uid','title', 'description', 'instruction', 'image_path','type', 'ingredients', 'preparation_time' , 
         'bevrage_type', 'bevrage_quantity','bevrage_option','bevrage_unit','bevrage_inclusion','total_calorie',
         'total_carbohydrate','total_protein','total_fat','total_sugar','total_cholestrol','is_deleted'
    ];
}
