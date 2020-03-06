<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImagePosts extends Model
{
    //
    protected $fillable = [
        'post_id','description','image_path','thumb_path'
    ];
    
    public function getImagePathAttribute($value)
    {
         if(!empty($value)){
             $arrayVal = explode(",",$value);
            
            return $arrayVal;
             }
             return ;
    }
    public function getThumbPathAttribute($value)
    {
         if(!empty($value)){
             $arrayVal = explode(",",$value);
            
            return $arrayVal;
             }
             return ;
    }
}
