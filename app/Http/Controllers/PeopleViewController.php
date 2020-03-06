<?php

namespace App\Http\Controllers;


use App\Services\PeopleViewService;
use Illuminate\Http\Request;

class PeopleViewController extends Controller {

    protected $peopleViewService;

    public function __construct(PeopleViewService $peopleViewService) {
        $this->peopleViewService = $peopleViewService;
    }
    /*  
     * 
     */
    public function getUserData(Request $request) {
        try {
                $userData = $this->peopleViewService->getUserData($request);
                return response()->json($userData ,200);

        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }
    
    public function blockUser (Request $request)
    {
        try{
            return response()->json(['status'=> true] , 200);
        } catch (\Exception $e)
        {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }

}
