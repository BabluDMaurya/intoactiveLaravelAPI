<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
class addVideosController extends Controller
{
    public function videoStore(Request $request){
//return response()->json(['status' => 'success'], 200);
        if ($request->hasFile('video')) { 
            $request->file('video')->storeAs('public/profile_pic/', date('His').'bablu.mp4');
            return response()->json(['status' => 'success'], 200);
        }else{
            return response()->json(['status' => 'fail'], 404);
        }
    }
}
