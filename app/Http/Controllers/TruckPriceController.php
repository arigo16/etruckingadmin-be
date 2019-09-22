<?php

namespace App\Http\Controllers;

use DB;
use Validator;
use App\Truck;
use App\TruckPrice;
use App\Vendor;
use App\TruckFeet;
use App\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\PromotionController as PromotionController;

class TruckPriceController extends Controller
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
        $truck_prices = TruckPrice::with("truck_type", "truck_feet")->get();
        foreach ($truck_prices as $key => $truck_price) {
            $truck_price['promotion'] = Promotion::where('order_type', $truck_price->order_type)->where('location_from', $truck_price->location_from)->where('location_to', $truck_price->location_to)->where('truck_type_id', $truck_price->truck_type_id)->where('truck_feet_id', $truck_price->truck_feet_id)->first();
            $truck_price['order_type_alias'] = $truck_price->orderTypeAlias($truck_price->order_type);
            $truck_price['detail_location_from'] = $truck_price->detailFrom($truck_price->order_type, $truck_price->location_from);
            $truck_price['detail_location_to'] = $truck_price->detailTo($truck_price->order_type, $truck_price->location_to);
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $truck_prices
        ], 200);   
    }

    // public function list(Request $request) {
    //     $result = collect();
    //     $temp = "";
    //     $truck_prices = TruckPrice::with("truck_type", "truck_feet")->get();
    //     foreach ($truck_prices as $key => $truck_price) {
    //         if($truck_price->truck_type_id != $temp){
    //             $collect = array(
    //                 "order_type" => $truck_price->order_type,
    //                 "truck_type_id" => $truck_price->truck_type_id,
    //                 "location_from" => $truck_price->location_from,
    //                 "location_to" => $truck_price->location_to,
    //             );
    //             $collect['promotion'] = Promotion::where('truck_type_id', $truck_price->truck_type_id)->get();
    //             $collect['order_type_alias'] = $truck_price->orderTypeAlias($truck_price->order_type);
    //             $collect['detail_location_from'] = $truck_price->detailFrom($truck_price->order_type, $truck_price->location_from);
    //             $collect['detail_location_to'] = $truck_price->detailTo($truck_price->order_type, $truck_price->location_to);
    //             $result->push($collect);
    //         }

    //         $temp = $truck_price->truck_type_id;
    //     }

    //     return response()->json([
    //         "status" => 200,
    //         "message" => "Get data successfully",
    //         "data" => $result
    //     ], 200);   
    // }

    public function detail(Request $request) {
        $truck_price = TruckPrice::with("truck_type", "truck_feet");

        if($request->order_type)
            $truck_price = $truck_price->where('order_type', $request->order_type);

        if($request->location_from)
            $truck_price = $truck_price->where('location_from', $request->location_from);

        if($request->location_to)
            $truck_price = $truck_price->where('location_from', $request->location_to);

        if($request->truck_type_id)
            $truck_price = $truck_price->where('truck_type_id', $request->truck_type_id);

        if($request->truck_feet_id)
            $truck_price = $truck_price->where('truck_feet_id', $request->truck_feet_id);

        $truck_price = $truck_price->get();

        foreach ($truck_price as $key => $f_truck_price) {
            $f_truck_price['order_type_alias'] = $f_truck_price->orderTypeAlias($f_truck_price->order_type);
            $f_truck_price['detail_location_from'] = $f_truck_price->detailFrom($f_truck_price->order_type, $f_truck_price->location_from);
            $f_truck_price['detail_location_to'] = $f_truck_price->detailTo($f_truck_price->order_type, $f_truck_price->location_to);
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $truck_price
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'order_type' => 'required',
            'location_from' => 'required',
            'location_to' => 'required',
            'truck_type_id' => 'required|exists:truck_types,id',
            'truck_feet_id' => 'required|exists:truck_feets,id',
            'price' => 'required|numeric'
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

        $is_exists = new TruckPrice;

        if($is_exists->checkAvailability($request->order_type, $request->location_from, $request->location_to, $request->truck_type_id, $request->truck_feet_id))
            return array(
                "status" => 401,
                "message" => "You could not create with the same order type, district, truck type and truck feet",
                "data" => array()
            );

        $truck_price = TruckPrice::create([
            'order_type' => $request->order_type,
            'location_from' => $request->location_from,
            'location_to' => $request->location_to,
            'truck_type_id' => $request->truck_type_id,
            'truck_feet_id' => $request->truck_feet_id,
            'price' => $request->price,
            'created_by' => $request->auth->id
        ]);

        if($truck_price){
            return response()->json([
               "status" => 200,
                "message" => "Truck price price created successfully",
                "data" => $truck_price
                ], 200);
        }

        else{
            return response()->json([
               "status" => 401,
                "message" => "Truck price failed to create successfully",
                "data" => $truck_price
                ], 401);
        }
    }

    public function update(Request $request) {
       $status = $message = "";
       $validator = Validator::make($request->all(), [
            'order_type' => 'required',
            'location_from' => 'required|exists:loc_districts,id',
            'location_to' => 'required|exists:loc_districts,id',
            'truck_type_id' => 'required|exists:truck_types,id',
            'truck_feet_id' => 'required|exists:truck_feets,id',
            'price' => 'required|numeric'
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

        $is_exists = new TruckPrice;

        if($is_exists->checkAvailability($request->order_type, $request->location_from, $request->location_to, $request->truck_type_id, $request->truck_feet_id))
            return array(
                "status" => 401,
                "message" => "You could not create with the same order type, district, truck type and truck feet",
                "data" => array()
            );
        
        $truck_price = TruckPrice::find($request->truck_price_id);

        if($truck_price){
            $truck_price->order_type = $request->order_type;
            $truck_price->location_from = $request->location_from;
            $truck_price->location_to = $request->location_to;
            $truck_price->truck_type_id = $request->truck_type_id;
            $truck_price->truck_feet_id = $request->truck_feet_id;
            $truck_price->price = $request->price;
            $truck_price->updated_by = $request->auth->id;

            $truck_price->update();

            $status = 200;
            $message = "Truck price updated successfully";
        }

        else{
            $status = 401;
            $message = "Truck price not found";
        }
        

        return response()->json([
           "status" => $status,
            "message" => $message,
            "data" => $truck_price
            ], $status);        
    }

    public function delete(Request $request) {
        $truck_price = TruckPrice::find($request->truck_price_id);

        if($truck_price){
            $truck_price->deleted_by = $request->auth->id;
            $truck_price->save();
            $truck_price->delete();
            $status = 200;
            $message = "Truck price deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a truck price failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $truck_price
        ], $status);        
    }

    public function createPriceAndPromo(Request $request) {
        $message = "";
        $status = "";
        $result = DB::transaction(function () use($request, $message, $status) {
            if($request->detail){
                foreach ($request->detail as $key => $detail) {
                    $is_exists = new TruckPrice;

                    if($is_exists->checkAvailability($request->order_type, $request->location_from, $request->location_to, $request->truck_type_id, $detail['truck_feet_id'])){
                        DB::rollback();

                        return array(
                            "status" => 401,
                            "message" => "You could not create with the same order type, district, truck type and truck feet"
                        );
                    }

                    $truck_price = TruckPrice::create([
                        'order_type' => $request->order_type,
                        'location_from' => $request->location_from,
                        'location_to' => $request->location_to,
                        'truck_type_id' => $request->truck_type_id,
                        'truck_feet_id' => $detail['truck_feet_id'],
                        'price' => $detail['price'],
                        'created_by' => $request->auth->id
                    ]);

                    if($truck_price){
                        $body = array(
                            "truck_feet_id" => $detail['truck_feet_id'],
                            "truck_type_id" => $request->truck_type_id,
                            "order_type" => $request->order_type,
                            "location_from" => $request->location_from,
                            "location_to" => $request->location_to,
                            "promo_nominal" => $detail['promo_nominal'],
                            "promo_alias" => $detail['promo_alias'],
                            "auth" => $request->auth->id
                        );

                        $promotion = PromotionController::create($body);

                        $status = $promotion['status'];
                        $message = $promotion['message'];
                    }
                }
            }

            return response()->json([
               "status" => $status,
                "message" => $message
                ], 200);
        });

        return $result;
    }


    public function createPriceAndPromoByExcel(Request $request) {
        $message = "";
        $status = "";
        $result = DB::transaction(function () use($request, $message, $status) {
            if($request->all()){
                foreach ($request->all() as $key => $req) {
                    $is_exists = new TruckPrice;

                    if($is_exists->checkAvailability($req['order_type'], $req['location_from'], $req['location_to'], $req['truck_type_id'], $req['truck_feet_id'])){
                        $exists_data = TruckPrice::where('order_type', $req['order_type'])
                            ->where('location_from', $req['location_from'])->where('location_to', $req['location_to'])
                            ->where('truck_type_id', $req['truck_type_id'])->where('truck_feet_id', $req['truck_feet_id'])
                            ->first();

                        $promotion = Promotion::where('order_type', $req['order_type'])
                            ->where('location_from', $req['location_from'])->where('location_to', $req['location_to'])
                            ->where('truck_type_id', $req['truck_type_id'])->where('truck_feet_id', $req['truck_feet_id'])
                            ->first();

                        $body = array_merge($req, array("promotion_id" => $promotion->id));

                        $res = self::updateProcess($exists_data, json_decode(json_encode($body)), $request->auth);
                        $status = $res['status'];
                        $message = $res['message'];
                    }


                    else{
                        $truck_price = TruckPrice::create([
                            'order_type' => $req['order_type'],
                            'location_from' => $req['location_from'],
                            'location_to' => $req['location_to'],
                            'truck_type_id' => $req['truck_type_id'],
                            'truck_feet_id' => $req['truck_feet_id'],
                            'price' => $req['price'],
                            'created_by' => $request->auth->id
                        ]);

                        if($truck_price){
                            $body = array(
                                "truck_feet_id" => $req['truck_feet_id'],
                                "truck_type_id" => $req['truck_type_id'],
                                "order_type" => $req['order_type'],
                                "location_from" => $req['location_from'],
                                "location_to" => $req['location_to'],
                                "promo_nominal" => $req['promo_nominal'],
                                "promo_alias" => $req['promo_alias'],
                                "auth" => $request->auth->id
                            );

                            $promotion = PromotionController::create($body);

                            $status = $promotion['status'];
                            $message = $promotion['message'];
                        }
                    }
                }
            }

            return response()->json([
               "status" => $status,
                "message" => $message
                ], 200);
        });

        return $result;
    }

    public function updatePriceAndPromo(Request $request) {
        $message = "";
        $status = "";
        $result = DB::transaction(function () use($request, $message, $status) {
            $is_exists = new TruckPrice;

            $exists_data = TruckPrice::where('order_type', $request->order_type)
                            ->where('location_from', $request->location_from)->where('location_to', $request->location_to)
                            ->where('truck_type_id', $request->truck_type_id)->where('truck_feet_id', $request->truck_feet_id)
                            ->first();
            
            $truck_price = TruckPrice::find($request->truck_price_id);
            

            if($exists_data){
                if($truck_price && $exists_data->is($truck_price)){
                    $res = self::updateProcess($truck_price, $request);
                    $status = $res['status'];
                    $message = $res['message'];
                }

                else{
                    DB::rollback();

                    return array(
                        "status" => 401,
                        "message" => "You could not create with the same order type, district, truck type and truck feet"
                    );
                }
            }

            else if($truck_price && !$exists_data){
                $res = self::updateProcess($truck_price, $request);
                $status = $res['status'];
                $message = $res['message'];
            }

            else{
                $status = 401;
                $message = "Truck price not found";
            }

            return response()->json([
               "status" => $status,
                "message" => $message
                ], 200);
        });

        return $result;
    }

    public function deletePriceAndPromo(Request $request) {
        $message = "";
        $status = "";
        $result = DB::transaction(function () use($request, $message, $status) {
            $truck_price = TruckPrice::find($request->truck_price_id);

            if($truck_price){
                $truck_price->deleted_by = $request->auth->id;
                $truck_price->save();
                $truck_price->delete();

                if($truck_price){
                    $body = array(
                        "promotion_id" => $request->promotion_id,
                        "auth" => $request->auth->id
                    );
                    $promotion = PromotionController::delete($body);
                    $status = 200;
                    $message = "Truck price deleted successfully";
                }
            }

            else{
                $status = 401;
                $message = "Delete a truck price failed";
            }

            return response()->json([
                "status" => $status,
                "message" => $message,
                "data" => $truck_price
            ], $status);  
        });     

        return $result; 
    }

    private static function updateProcess($truck_price, $request, $auth = Null) {
        if(isset($request->auth))
            $auth = $request->auth->id;
        else
            $auth = $auth->id;

        $status = $message = "";
        $truck_price->order_type = $request->order_type;
        $truck_price->location_from = $request->location_from;
        $truck_price->location_to = $request->location_to;
        $truck_price->truck_type_id = $request->truck_type_id;
        $truck_price->truck_feet_id = $request->truck_feet_id;
        $truck_price->price = $request->price;
        $truck_price->updated_by = $auth;

        $truck_price->update();

        if($truck_price){
            $body = array(
                "truck_feet_id" => $request->truck_feet_id,
                "truck_type_id" => $request->truck_type_id,
                "order_type" => $request->order_type,
                "location_from" => $request->location_from,
                "location_to" => $request->location_to,
                "promo_nominal" => $request->promo_nominal,
                "promo_alias" => $request->promo_alias,
                "promotion_id" => $request->promotion_id,
                "auth" => $auth
            );

            $promotion = PromotionController::update($body);

            $status = $promotion['status'];
            $message = $promotion['message'];
        }

        else{
            $status = 400;
            $message = "Failed to update truck price"; 
        }

        return array(
            "status" => $status,
            "message" => $message
        );
    }
}
