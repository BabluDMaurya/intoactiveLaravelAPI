<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AddVideosService;

class addVideosController extends Controller {

    public function __construct(AddVideosService $videoService) {
        $this->videoService = $videoService;
    }

    public function addVideo(Request $request) {
        try {
            $this->videoService->videoStore($request);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }
    public function updateVideo(Request $request) {
        try {
            $this->videoService->videoUpdate($request);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }

}
