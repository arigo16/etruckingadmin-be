<?php

namespace App\Http\Controllers;

use Validator;
use App\Seaport;
use Illuminate\Http\Request;

class SeaportController extends Controller
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
       $seaports = Seaport::all();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $seaports
        ], 200);   
    }

    public function detail(Request $request) {
        $seaport = Seaport::where('id', $request->seaport_id)->first();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $seaport
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'seaport_name' => 'required',
            'lat_seaport' => 'required',
            'long_seaport' => 'required'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create seaport failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $seaport = Seaport::create([
            'seaport_name' => $request->seaport_name,
            'lat_seaport' => $request->lat_seaport,
            'long_seaport' => $request->long_seaport,
            'created_by' => $request->auth->id
        ]);

        if($seaport){
            return response()->json([
               "status" => 200,
                "message" => "Seaport created successfully",
                "data" => $seaport
                ], 200);
        }

        else{
            return response()->json([
               "status" => 401,
                "message" => "Seaport failed to create successfully",
                "data" => $seaport
                ], 401);
        }
    }

    public function update(Request $request) {
       $status = $message = "";

        $validator = Validator::make($request->all(), [
            'seaport_name' => 'required',
            'lat_seaport' => 'required',
            'long_seaport' => 'required'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Update seaport failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $seaport = Seaport::find($request->seaport_id);

        if($seaport){
            $seaport->seaport_name = $request->seaport_name;
            $seaport->lat_seaport = $request->lat_seaport;
            $seaport->long_seaport = $request->long_seaport;
            $seaport->updated_by = $request->auth->id;
            $seaport->update();
            
            $status = 200;
            $message = "Seaport updated successfully";
        }

        else{
            $status = 410;
            $message = "Seaport not found";
        }
        

        return response()->json([
           "status" => $status,
            "message" => $message,
            "data" => $seaport
            ], $status);        
    }

    public function delete(Request $request) {
        $seaport = Seaport::find($request->seaport_id);

        if($seaport){
            $seaport->deleted_by = $request->auth->id;
            $seaport->save();
            $seaport->delete();
            $status = 200;
            $message = "Seaport deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a seaport failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $seaport
        ], $status);        
    }

    public function trash()
    {
        $seaport = Seaport::onlyTrashed()->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $seaport
        ], 200);       
    }
}
