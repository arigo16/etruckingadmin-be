<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Validator;
use App\LocProvince;
use App\LocRegency;
use App\LocDistrict;

class LocationController  extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function province(Request $request) {
        $provinces = LocProvince::with('regency');

        $provinces = $provinces->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $provinces
        ], 200);   
    }

    public function regency(Request $request) {
        $regencies = LocRegency::with('province', 'district');

        if($request->regency_id)
            $regencies = $regencies->where('id', $request->regency_id);

        $regencies = $regencies->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $regencies
        ], 200);   
    }

    public function district(Request $request) {
        $districts = LocDistrict::with('regency', 'regency.province');

        if($request->district_id)
            $districts = $districts->where('id', $request->district_id);

        $districts = $districts->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $districts
        ], 200);   
    }

    public function getAllDistrict(Request $request) {
        $districts = LocDistrict::with('regency', 'regency.province')->get();

        foreach ($districts as $key => $district) {
            if($district->regency){
                if($district->regency->province){
                    $district['province_id'] = $district->regency->province->id;
                    $district['province_name'] = $district->regency->province->name;
                }
                
                $district['regency_id'] = $district->regency->id;
                $district['regency_name'] = $district->regency->name;
                unset($district->regency);
            }
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $districts
        ], 200);  
    }
}
