<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestEmail;
use Mail;

class HomeController extends Controller{
    
    public function index()
    {
        $data = ['message' => 'This is a test!'];
        Mail::to('bablu@wdipl.com')->send(new TestEmail($data));
        return "This is test mail return";
    }
}
