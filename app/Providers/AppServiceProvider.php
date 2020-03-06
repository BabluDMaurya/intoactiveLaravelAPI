<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         //
        Schema::defaultStringLength(191);
       Validator::extend('emailformate', function ($attribute, $value, $parameters, $validator) {
            $regex = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/";      
                if(preg_match($regex, $value)) {
                      return true; 
                }else{
                    return false;
                }            
        });
        
         Validator::replacer('emailformate', function($message, $attribute, $rule, $parameters) {
            return str_replace($message, "Please enter Correct Email formate Like: example@domain.com", $message);
        });
        
         Validator::extend('emaildomain', function ($attribute, $value, $parameters, $validator) {
            $regex = "/^[A-Za-z0-9\.]*@(wdipl|yopmail|gmail|yahoo|live|outlook|hotmail)[.](com)$/";      
                if(preg_match($regex, $value)) {
                      return true; 
                }else{
                    return false;
                }
            
        });        
        Validator::replacer('emaildomain', function($message, $attribute, $rule, $parameters) {
            return str_replace($message, "We appreciate your interest on using our System. However at the moment we offer this service only to wdipl.com ,gmail.com ,yopmail.com, yahoo ,hotmail.com!", $message);
        });
    }
}
