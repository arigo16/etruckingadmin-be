<?php

namespace App\Http\Controllers;

use Validator;
use App\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
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

    public function list(Request $request) {
       $depots = Depot::all();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $depots
        ], 200);   
    }

    public function detail(Request $request) {
        $depot = Depot::where('id', $request->depot_id)->first();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $depot
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'depot_name' => 'required',
            'depot_address' => 'required',
            'depot_email' => 'required|email',
            'depot_phone' => 'required',
            'depot_price' => 'required|numeric'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create depot failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $depot = Depot::create([
            'depot_name' => $request->depot_name,
            'depot_address' => $request->depot_address,
            'depot_email' => $request->depot_email,
            'depot_phone' => $request->depot_phone,
            'depot_price' => $request->depot_price,
            'created_by' => $request->auth->id
        ]);

        if($depot){
            return response()->json([
               "status" => 200,
                "message" => "Depot created successfully",
                "data" => $depot
                ], 200);
        }

        else{
            return response()->json([
               "status" => 401,
                "message" => "Depot failed to create successfully",
                "data" => $depot
                ], 401);
        }
    }

    public function update(Request $request) {
       $status = $message = "";

        $validator = Validator::make($request->all(), [
            'depot_name' => 'required',
            'depot_address' => 'required',
            'depot_email' => 'required|email',
            'depot_phone' => 'required',
            'depot_price' => 'required|numeric'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Update depot failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $depot = Depot::find($request->depot_id);

        if($depot){
            $depot->depot_name = $request->depot_name;
            $depot->depot_address = $request->depot_address;
            $depot->depot_email = $request->depot_email;
            $depot->depot_phone = $request->depot_phone;
            $depot->depot_price = $request->depot_price;
            $depot->updated_by = $request->auth->id;
            $depot->update();
            
            $status = 200;
            $message = "Depot updated successfully";
        }

        else{
            $status = 410;
            $message = "Depot not found";
        }
        

        return response()->json([
           "status" => $status,
            "message" => $message,
            "data" => $depot
            ], $status);        
    }

    public function delete(Request $request) {
        $depot = Depot::find($request->depot_id);

        if($depot){
            $depot->deleted_by = $request->auth->id;
            $depot->save();
            $depot->delete();
            $status = 200;
            $message = "Depot deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a depot failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $depot
        ], $status);        
    }

    public function trash()
    {
        $depot = Depot::onlyTrashed()->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $depot
        ], 200);       
    }
}
