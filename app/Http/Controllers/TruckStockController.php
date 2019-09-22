<?php

namespace App\Http\Controllers;

use Validator;
use App\Truck;
use App\TruckStock;
use App\Vendor;
use App\TruckFeet;
use Illuminate\Http\Request;

class TruckStockController extends Controller
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
        $truck_prices = TruckStock::with("truck", "vendor")->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $truck_prices
        ], 200);   
    }

    public function detail(Request $request) {
        $truck_price = TruckStock::with('location', "truck_type", "truck_feet");

        if($request->order_type)
            $truck_price = $truck_price->where('order_type', $request->order_type);

        if($request->district_id)
            $truck_price = $truck_price->where('district_id', $request->district_id);

        if($request->truck_type_id)
            $truck_price = $truck_price->where('truck_type_id', $request->truck_type_id);

        if($request->truck_feet_id)
            $truck_price = $truck_price->where('truck_feet_id', $request->truck_feet_id);

            
        $truck_price = $truck_price->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $truck_price
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|exists:trucks,id',
            'vendor_id' => 'required|exists:vendors,id',
            'qty_actual' => 'numeric',
            'qty_available' => 'numeric'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create truck stock failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $is_exists = new TruckStock;

        if($is_exists->checkAvailability($request->vendor_id, $request->truck_id))
            return array(
                "status" => 401,
                "message" => "You could not create with the same vendor and truck",
                "data" => array()
            );

        $truck_stock = TruckStock::create([
            'truck_id' => $request->truck_id,
            'vendor_id' => $request->vendor_id,
            'qty_actual' => $request->qty_actual,
            'qty_available' => $request->qty_available,
            'created_by' => $request->auth->id
        ]);

        if($truck_stock){
            return response()->json([
               "status" => 200,
                "message" => "Truck stock stock created successfully",
                "data" => $truck_stock
                ], 200);
        }

        else{
            return response()->json([
               "status" => 401,
                "message" => "Truck stock failed to create",
                "data" => $truck_stock
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

        $is_exists = new TruckStock;

        if($is_exists->checkAvailability($request->order_type, $request->district_id, $request->truck_type_id, $request->truck_feet_id))
            return array(
                "status" => 401,
                "message" => "You could not create with the same order type, district, truck type and truck feet",
                "data" => array()
            );
        
        $truck_stock = TruckStock::find($request->truck_stock_id);

        if($truck_stock){
            $truck_stock->truck_id = $request->truck_id;
            $truck_stock->vendor_id = $request->vendor_id;
            $truck_stock->qty_actual = $request->qty_actual;
            $truck_stock->qty_available = $request->qty_available;
            $truck_stock->updated_by = $request->auth->id;
            $truck_stock->update();

            $status = 200;
            $message = "Truck stock updated successfully";
        }

        else{
            $status = 401;
            $message = "Truck stock not found";
        }
        

        return response()->json([
           "status" => $status,
            "message" => $message,
            "data" => $truck_stock
            ], $status);        
    }

    public function delete(Request $request) {
        $truck_stock = TruckStock::find($request->truck_stock_id);

        if($truck_stock){
            $truck_stock->deleted_by = $request->auth->id;
            $truck_stock->save();
            $truck_stock->delete();
            $status = 200;
            $message = "Truck stock deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a truck stock failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $truck_stock
        ], $status);        
    }
}
