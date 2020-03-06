<?php

namespace App\Services;

use DB;
use Mail;
use Auth;
use Exception;
use App\Model\User;


class SearchService {

    public function getUsers($request)
    {
         try {
           $arr = explode(',',$request['userListedId']);
             $usersList = User::whereNotIn('id',  $arr)->orderBy("user_name",'Asc')->get(); 
             $usersList->map(function ($ip) {
                return $ip->bios;
            });
             foreach($usersList as $key => $uList)
             {
               $val = strtoupper(substr($uList['user_name'],0,1));               
               $sortedList[$val][] =  $uList;
             }        
                      return $usersList;
         } catch (Exception $ex) {
            throw $ex;
         }
    }
    
     public function searchRequest($request)
    {
        try {
            
            $searchQuery = $request['searchQuery'];  
          
            $usersList = User::where('user_name', 'like', $searchQuery.'%')->orderBy("user_name",'Asc')->get();
            $usersList->map(function ($ip) {
                return $ip->bios;
            });
            return $usersList;
            
         } catch (Exception $ex) {
            throw $ex;
         }
    }

}
