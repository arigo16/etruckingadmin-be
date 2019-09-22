<?php

namespace App\Http\Controllers;

use Validator;
use App\Truck;
use App\Vendor;
use App\TruckFeet;
use App\TruckType;
use App\BoxType;
use Illuminate\Http\Request;

class TruckController extends Controller
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
        $trucks = Truck::with("vendor", "truck_stock", "truck_type", "box_type", "truck_feet")->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $trucks
        ], 200);   
    }

    public function detail(Request $request) {
        $truck = Truck::with("truck_type", "box_type", "truck_feet")->where('id', $request->truck_id);

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $truck
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'truck_type_id' => 'required|exists:truck_types,id',
            'box_type_id' => 'required|exists:box_types,id',
            'truck_feet_id' => 'required|exists:truck_feets,id',
            'plat_number' => 'required|unique:trucks',
            'merk' => 'required',
            'model' => 'required',
            'status' => 'required',
            'image_stnk' => 'required',
            'image_interior' => 'required',
            'image_front' => 'required',
            'image_back' => 'required',
            'image_kir_head' => 'required',
            'kir_head_number' => 'required',
            'kir_head_expiry' => 'required',
            'image_kir_trailer' => 'required',
            'kir_trailer_number' => 'required',
            'kir_trailer_expiry' => 'required'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create truck failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $truck = Truck::create([
            'vendor_id' => $request->vendor_id,
            'truck_type_id' => $request->truck_type_id,
            'box_type_id' => $request->box_type_id,
            'truck_feet_id' => $request->truck_feet_id,
            'plat_number' => $request->plat_number,
            'merk' => $request->merk,
            'model' => $request->model,
            'status' => $request->status,
            'image_stnk' => $request->image_stnk,
            'image_interior' => $request->image_interior,
            'image_front' => $request->image_front,
            'image_back' => $request->image_back,
            'image_kir_head' => $request->image_kir_head,
            'kir_head_number' => $request->kir_head_number,
            'kir_head_expiry' => $request->kir_head_expiry,
            'image_kir_trailer' => $request->image_kir_trailer,
            'kir_trailer_number' => $request->kir_trailer_number,
            'kir_trailer_expiry' => $request->kir_trailer_expiry,
            'created_by' => $request->auth->id
        ]);

        if($truck){
            return response()->json([
               "status" => 200,
                "message" => "Truck created successfully",
                "data" => $truck
                ], 200);
        }

        else{
            return response()->json([
               "status" => 401,
                "message" => "Truck failed to create successfully",
                "data" => $truck
                ], 401);
        }
    }

    public function update(Request $request) {
       $status = $message = "";
       $validator = Validator::make($request->all(), [
            'truck_type_id' => 'exists:truck_types,id',
            'plat_number' => 'unique:trucks,id,'.$request->truck_id,
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Update truck failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $truck = Truck::find($request->truck_id);

        if($truck){
            $truck->vendor_id = $request->vendor_id;
            $truck->truck_type_id = $request->truck_type_id;
            $truck->box_type_id = $request->box_type_id;
            $truck->plat_number = $request->plat_number;
            $truck->truck_feet_id = $request->truck_feet_id;
            $truck->merk = $request->merk;
            $truck->model = $request->model;
            $truck->status = $request->status;
            $truck->image_stnk = $request->image_stnk;
            $truck->image_interior = $request->image_interior;
            $truck->image_front = $request->image_front;
            $truck->image_back = $request->image_back;
            $truck->updated_by = $request->auth->id;
            $truck->image_kir_head = $request->image_kir_head;
            $truck->kir_head_number = $request->kir_head_number;
            $truck->kir_head_expiry = $request->kir_head_expiry;
            $truck->image_kir_trailer = $request->image_kir_trailer;
            $truck->kir_trailer_number = $request->kir_trailer_number;
            $truck->kir_trailer_expiry = $request->kir_trailer_expiry;
            $truck->updated_by = $request->auth->id;
            $truck->update();

            $status = 200;
            $message = "Truck updated successfully";
        }

        else{
            $status = 401;
            $message = "Truck not found";
        }
        

        return response()->json([
           "status" => $status,
            "message" => $message,
            "data" => $truck
            ], $status);        
    }

    public function delete(Request $request) {
        $truck = Truck::find($request->truck_id);

        if($truck){
            $truck->deleted_by = $request->auth->id;
            $truck->save();
            $truck->delete();
            $status = 200;
            $message = "Truck deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a truck failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $truck
        ], $status);        
    }

    public function trash()
    {
        $truck = Truck::onlyTrashed()->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $truck
        ], 200);       
    }

    public function getTruckFeets(){
        $feets = TruckFeet::with("trucks")->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $feets
        ], 200);   
    }

    public function getTruckType(){
        $types = TruckType::with("trucks")->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $types
        ], 200);   
    }

    public function getBoxType(){
        $box = BoxType::with("trucks")->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $box
        ], 200);   
    }
}
