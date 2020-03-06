<?php

namespace App\Http\Controllers;

use App\Services\NutritionService;

use App\Http\Requests\NutritionRequest;
use Illuminate\Http\Request;

class NutritionController extends Controller {

    //
    protected $nutritionService;

    public function __construct(NutritionService $nutritionService) {
        $this->nutritionService = $nutritionService;
    }

    public function insert(NutritionRequest $request) {

        try {
            $status = $this->nutritionService->insertNutrition($request);
            return response()->json(['status' => $status], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
