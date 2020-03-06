<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use Auth;
use App\Model\User;
class SearchController extends Controller
{
    //
    protected $searchService;

    public function __construct(SearchService $searchService) {
        $this->searchService = $searchService;
    }
    
    public function topTenPeople(){
        try { 
            
            $userData = Auth::User();
        
//             $usersList = User::where('user_name', 'like', $request->peopleName.'%')->get();
             $countryList = User::whereNotIn('id',  [$userData->id])->where('location', $userData->location)->orderBy('created_at', 'desc')->take(5)->get();
             $imgPost = $countryList->map(function ($ip) {
               
                return $ip->bios;
            });
             $countryArr = Array();
             foreach($countryList as $cList ){
                 $countryArr []=$cList['id'];
             }
             $countryArr =  implode(',',$countryArr );
            return response()->json(['topTenUserList'=>$countryList,'topTenID' => $countryArr], 200);
//            return response()->json(['topTenUserList'=>$countryList], 200);            
        } catch (Exception $ex) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function searchPeople(Request $request)
    {
//        $userList = $this->searchService->getUsers($request);
//        dd($userList);
        try {
            $userList = $this->searchService->getUsers($request);
            return response()->json(['userList'=>$userList], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function searchRequest(Request $request)
    {
//        $userList = $this->searchService->getUsers($request);
//        dd($userList);
        try {
            $userList = $this->searchService->searchRequest($request);
            return response()->json(['searchList'=>$userList], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
