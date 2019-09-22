<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use DB;
use Validator;
use App\Promotion;
use App\Vendor;
use App\Truck;
use App\LocDistrict;

class PromotionController extends Controller
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
        $promotions = Promotion::with("vendor", "truck_type", "truck_feet")->get();
        foreach ($promotions as $key => $promotion) {
            $promotion['order_type_alias'] = $promotion->orderTypeAlias($promotion->order_type);
            $promotion['detail_location_from'] = $promotion->detailFrom($promotion->order_type, $promotion->location_from);
            $promotion['detail_location_to'] = $promotion->detailTo($promotion->order_type, $promotion->location_to);
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $promotions
        ], 200);   
    }

    public function detail(Request $request) {
        $promotion = Promotion::with("vendor", "truck_type", "truck_feet");

        if($request->vendor_id)
            $promotion = $promotion->where('vendor_id', $request->vendor_id);

        if($request->order_type)
            $promotion = $promotion->where('order_type', $request->order_type);

        if($request->location_from)
            $truck_price = $truck_price->where('location_from', $request->location_from);

        if($request->location_to)
            $truck_price = $truck_price->where('location_from', $request->location_to);

        if($request->truck_feet_id)
            $promotion = $promotion->where('truck_feet_id', $request->truck_feet_id);

        if($request->truck_type_id)
            $promotion = $promotion->where('truck_type_id', $request->truck_type_id);
            
        $promotion = $promotion->get();

        foreach ($promotion as $key => $f_promotion) {
            $f_promotion['order_type_alias'] = $f_promotion->orderTypeAlias($f_promotion->order_type);
            $f_promotion['detail_location_from'] = $f_promotion->detailFrom($f_promotion->order_type, $f_promotion->location_from);
            $f_promotion['detail_location_to'] = $f_promotion->detailTo($f_promotion->order_type, $f_promotion->location_to);
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $promotion
        ], 200);  
    }

    // public function create(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'order_type' => 'required|in:1,2,3',
    //         'truck_feet_id' => 'required|exists:truck_feets,id',
    //         'truck_type_id' => 'required|exists:truck_types,id',
    //         'vendor_id' => 'required|exists:vendors,id',
    //         'location_from' => 'required',
    //         'location_to' => 'required',
    //         'promo_nominal' => 'required|numeric',
    //         'promo_alias' => 'required|numeric',
    //     ]);

    //     if($validator->fails()){
    //         return array(
    //             "status" => 401,
    //             "message" => "Create promotion failed",
    //             "data" => array(
    //                 "error" => array_map(function($values) {
    //                                 return join(',', $values);
    //                             }, array_values($validator->errors()->toArray()))
    //             )
    //         );
    //     }

    //     $is_exists = new Promotion;

    //     if($is_exists->checkAvailability($request->vendor_id, $request->order_type, $request->location_from, $request->location_to, $request->truck_feet_id, $request->truck_type_id))
    //         return array(
    //             "status" => 401,
    //             "message" => "You could not create with the same vendor, truck, and location",
    //             "data" => array()
    //         );

    //     $promotion = Promotion::create([
    //         'order_type' => $request->order_type,
    //         'truck_feet_id' => $request->truck_feet_id,
    //         'truck_type_id' => $request->truck_type_id,
    //         'vendor_id' => $request->vendor_id,
    //         'location_from' => $request->location_from,
    //         'location_to' => $request->location_to,
    //         'promo_nominal' => $request->promo_nominal,
    //         'promo_alias' => $request->promo_alias,
    //         'created_by' => $request->auth->id
    //     ]);

    //     if($promotion){
    //         return response()->json([
    //            "status" => 200,
    //             "message" => "Promotion created successfully",
    //             "data" => $promotion
    //             ], 200);
    //     }

    //     else{
    //         return response()->json([
    //            "status" => 401,
    //             "message" => "Promotion failed to create",
    //             "data" => $promotion
    //             ], 401);
    //     }
    // }

    // public function update(Request $request) {
    //     $status = $message = "";
    //     $validator = Validator::make($request->all(), [
    //         'order_type' => 'required|in:1,2,3',
    //         'truck_feet_id' => 'required|exists:truck_feets,id',
    //         'truck_type_id' => 'required|exists:truck_types,id',
    //         'vendor_id' => 'required|exists:vendors,id',
    //         'location_from' => 'required',
    //         'location_to' => 'required',
    //         'promo_nominal' => 'required|numeric',
    //         'promo_alias' => 'required|numeric',
    //     ]);

    //     if($validator->fails()){
    //         return array(
    //             "status" => 401,
    //             "message" => "Update promotion failed",
    //             "data" => array(
    //                 "error" => array_map(function($values) {
    //                                 return join(',', $values);
    //                             }, array_values($validator->errors()->toArray()))
    //             )
    //         );
    //     }

    //     $is_exists = new Promotion;
        
    //     if($is_exists->checkAvailability($request->vendor_id, $request->order_type, $request->location_from, $request->location_to, $request->truck_feet_id, $request->truck_type_id))
    //         return array(
    //             "status" => 401,
    //             "message" => "You could not create with the same vendor, truck, and location",
    //             "data" => array()
    //         );

    //     $promotion = Promotion::find($request->promotion_id);

    //     if($promotion){
    //         $promotion->order_type = $request->order_type;
    //         $promotion->truck_feet_id = $request->truck_feet_id;
    //         $promotion->truck_type_id = $request->truck_type_id;
    //         $promotion->vendor_id = $request->vendor_id;
    //         $promotion->location_from = $request->location_from;
    //         $promotion->location_to = $request->location_to;
    //         $promotion->promo_nominal = $request->promo_nominal;
    //         $promotion->promo_alias = $request->promo_alias;
    //         $promotion->updated_by = $request->auth->id;
    //         $promotion->update();

    //         $status = 200;
    //         $message = "Promotion updated successfully";
    //     }

    //     else{
    //         $status = 401;
    //         $message = "Promotion not found";
    //     }
        

    //     return response()->json([
    //        "status" => $status,
    //         "message" => $message,
    //         "data" => $promotion
    //         ], $status);        
    // }

    // public function delete(Request $request) {
    //     $promotion = Promotion::find($request->promotion_id);

    //     if($promotion){
    //         $promotion->deleted_by = $request->auth->id;
    //         $promotion->save();
    //         $promotion->delete();
    //         $status = 200;
    //         $message = "Promotion deleted successfully";
    //     }

    //     else{
    //         $status = 401;
    //         $message = "Delete a promotion failed";
    //     }

    //     return response()->json([
    //         "status" => $status,
    //         "message" => $message,
    //         "data" => $promotion
    //     ], $status);        
    // }

    public static function create($request) {
        $is_exists = new Promotion;

        if($is_exists->checkAvailability($request['order_type'], $request['location_from'], $request['location_to'], $request['truck_feet_id'], $request['truck_type_id'])){
            DB::rollback();
            return array(
                "status" => 401,
                "message" => "You could not create with the same truck, and location"
            );
        }

        $promotion = Promotion::create([
            'order_type' => $request['order_type'],
            'truck_feet_id' => $request['truck_feet_id'],
            'truck_type_id' => $request['truck_type_id'],
            'location_from' => $request['location_from'],
            'location_to' => $request['location_to'],
            'promo_nominal' => $request['promo_nominal'],
            'promo_alias' => $request['promo_alias'],
            'created_by' => $request['auth']
        ]);

        if($promotion){
            return array(
                "status" => 200,
                "message" => "Price and promotion created successfully"
            );
        }

        else{
            DB::rollback();
            return array(
                "status" => 401,
                "message" => "Price and promotion failed to create",
            );
        }
    }

    public static function update($request) {
        $message = "";
        $status = "";
        // $is_exists = new Promotion;

        // $exists_data = Promotion::where('order_type', $request['order_type'])
        //                 ->where('location_from', $request['location_from'])->where('location_to', $request['location_to'])
        //                 ->where('truck_type_id', $request['truck_type_id'])->where('truck_feet_id', $request['truck_feet_id'])
        //                 ->first();

        // $promotion = Promotion::find($request['promotion_id']);

        // if($exists_data){
        //     if($promotion && $exists_data->is($promotion)){
        //         $res = self::updateProcess($promotion, $request);
        //         $status = $res['status'];
        //         $message = $res['message'];
        //     }
        // }

        // else if($promotion && !$exists_data){
        //     $res = self::updateProcess($promotion, $request);
        //     $status = $res['status'];
        //     $message = $res['message'];
        // }

        $promotion = Promotion::find($request['promotion_id']);

        if($promotion){
            $res = self::updateProcess($promotion, $request);
            $status = $res['status'];
            $message = $res['message'];
        }

        else{
            $status = 401;
            $message = "Promotion not found";
        }

        return array(
            "status" => $status,
            "message" => $message
        );
    }

    public static function delete($request) {
        $promotion = Promotion::find($request['promotion_id']);

        if($promotion){
            $promotion->deleted_by = $request['auth'];
            $promotion->save();
            $promotion->delete();

            if($promotion){
                return array(
                    "status" => 200,
                    "message" => "Price and promotion deleted successfully"
                );
            }

            else{
                DB::rollback();
                return array(
                    "status" => 401,
                    "message" => "Price and promotion failed to delete",
                );
            }
        }

        DB::rollback();
        return array(
            "status" => 401,
            "message" => "Price and promotion failed to delete",
        );      
    }

    private static function updateProcess($promotion, $request) {
        if($promotion){
            $promotion->order_type = $request['order_type'];
            $promotion->truck_feet_id = $request['truck_feet_id'];
            $promotion->truck_type_id = $request['truck_type_id'];
            $promotion->location_from = $request['location_from'];
            $promotion->location_to = $request['location_to'];
            $promotion->promo_nominal = $request['promo_nominal'];
            $promotion->promo_alias = $request['promo_alias'];
            $promotion->updated_by = $request['auth'];
            $promotion->update();

            if($promotion){
                return array(
                    "status" => 200,
                    "message" => "Price and promotion updated successfully"
                );
            }

            else{
                DB::rollback();
                return array(
                    "status" => 401,
                    "message" => "Price and promotion failed to update",
                );
            }
        }

        else{
            $status = 400;
            $message = "Failed to price and promotion"; 
        }

        return array(
            "status" => $status,
            "message" => $message
        );
    }
}
