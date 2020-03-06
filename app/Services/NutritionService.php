<?php

namespace App\Services;

use DB;
use Mail;
use Auth;
use Exception;
use App\Model\Nutrition;

class NutritionService {

    public function insertNutrition($request) {
        //
        try {
            $userData = Auth::User();
            //print_r($userData);
            //die('sasd');
            $instruction = null;
            $ingredient = null;
            foreach ($request->nutriInstruction as $key => $val) {
                $instruction .= $val['name'] . ',';
            }
            

            if ($request->apiData) {
                $ingredient .=  $request->apiData;
            }
            foreach ($request->nutriIngredients as $key => $val) {
                if($val['name']!='')
                {
                    $ingredient .=  ','.$val['name'] ;
                }
            }
            DB::beginTransaction();
            Nutrition::create([
                'uid' =>$userData->id,
                'title' => $request->nutriTitle,
                'description' => $request->nutriDescription,
                'instruction' => $instruction,
                'image_path' => '',
                'type' => $request->nutriMealType,
                'ingredients' => $ingredient,
                'preparation_time' => $request->nutriPrepHrs . ':' . $request->nutriPrepMin,
                'bevrage_type' => $request->nutriBevrageType,
                'bevrage_quantity' => $request->bevragveQnty,
                'bevrage_option' => $request->bevragveOption,
                'bevrage_unit' => $request->bevragveUnit,
                'bevrage_inclusion' => $request->nutriBevrageType,
                'total_calorie' => $request->totalCal,
                'total_carbohydrate' => $request->totalCarbo,
                'total_protein' => $request->totalProt,
                'total_fat' => $request->totalFat,
                'total_sugar' => $request->totalSug,
                'total_cholestrol' => $request->totalChol,
            ]);

            DB::commit();
            return $status = true;
        } catch (\Exception $e) {
             DB::rollBack();
            throw $e;
        }
    }

}
