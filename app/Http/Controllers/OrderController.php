<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Validator;
use App\Promotion;
use App\Truck;
use App\Vendor;
use App\TruckFeet;
use App\TruckType;
use App\BoxType;
use App\Driver;
use App\Order;
use App\OrderDetail;
use App\OrderDriverTruck;

use App\Http\Controllers\NotificationCOntroller;

class OrderController extends Controller
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

    public function verifiedOrder() {
    	$orders = OrderDetail::with('order', 'order.customer', 'order.customer.accounts', 'vendor', 'truck_feet', 'truck_type', 'documents', 'payment')->where('status_detail', 4)->get();

        if($orders){
            foreach ($orders as $key => $order) {
                $order['order_type_alias'] = $order->order->orderTypeAlias($order->order->order_type);
                $order['order']['location_pickup'] = $order->order->location_pickup($order->order->order_type, $order->order->loc_pickup);
                $order['order']['location_delivery'] = $order->order->location_delivery($order->order->order_type, $order->order->loc_delivery);
            }
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $orders
        ], 200);
    }

    public function listMonitoring() {
    	$result = [];
		$orders = \DB::select('select * from vw_order_histories');

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $orders
        ], 200);
    }

    public function getOrderConfirmation() {
        $orders = OrderDetail::with('order', 'order.customer', 'order.customer.accounts', 'vendor', 'truck_feet', 'truck_type', 'payment', 'documents')->where([['is_payment', '=', 1], ['status_detail', '=', null]])->orWhere([['is_payment', '=', 1], ['status_detail', '=', 1]])->orWhere([['is_document', '=', 1], ['status_detail', '=', 2]])->orWhere([['is_document', '=', 1], ['status_detail', '=', 3]])->get();

        if($orders){
            foreach ($orders as $key => $order) {
                $order['order_type_alias'] = $order->order->orderTypeAlias($order->order->order_type);
                $order['order']['location_pickup'] = $order->order->location_pickup($order->order->order_type, $order->order->loc_pickup);
                $order['order']['location_delivery'] = $order->order->location_delivery($order->order->order_type, $order->order->loc_delivery);
            }
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $orders
        ], 200);
    }


    public function getOrderInDetail(Request $request) {
    	$orders = OrderDetail::with('vendor', 'order', 'order.customer', 'order.customer.user_aliases', 'order.customer.accounts', 'documents', 'order_history.driver', 'order_history.truck', 'payment')->where('id', $request->order_detail_id)->get();

        if($orders){
            foreach ($orders as $key => $order) {
                $order['order_type_alias'] = $order->order->orderTypeAlias($order->order->order_type);
                $order['order']['location_pickup'] = $order->order->location_pickup($order->order->order_type, $order->order->loc_pickup);
                $order['order']['location_delivery'] = $order->order->location_delivery($order->order->order_type, $order->order->loc_delivery);
            }
        }

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $orders[0]
        ], 200);
    }

    public function availableTruck($vendor_id, $truck_type_id, $truck_feet_id) {
    	$result = [];

    	$ongoing_order = OrderDetail::where('is_document', 1)->where('is_payment', 1)->Where('status_detail', 1)->pluck('id');
    	$ongoing_truck = OrderDriverTruck::whereIn('order_detail_id', $ongoing_order)->pluck('truck_id');

    	$trucks = Truck::with(['truck_stock' => function ($query) {
    		$query->where('qty_available', '!=', 0);
    	}])->where('vendor_id', $vendor_id)->where('truck_type_id', $truck_type_id)->where('truck_feet_id', $truck_type_id)->whereNotIn('id', $ongoing_truck)->get();

    	foreach ($trucks as $key => $truck) {
    		if($truck->truck_stock)
    			$result[] = $truck;
		}
		
		return response()->json([
			"status" => 200,
			"message" => "Get successfully",
			"data" => $result
		], 200);
    }


    public function assignDriver($vendor_id) {
    	$ongoing_order = OrderDetail::where('is_document', 1)->where('is_payment', 1)->Where('status_detail', 1)->pluck('id');
    	$ongoing_driver = OrderDriverTruck::whereIn('order_detail_id', $ongoing_order)->pluck('driver_id');
    	$drivers = Driver::where('vendor_id', $vendor_id)->whereNotIn('id', $ongoing_driver)->get();
    	return response()->json([
			"status" => 200,
			"message" => "Get successfully",
			"data" => $drivers
		], 200);
    }

    public function confirmationPayment($action, Request $request) {
        $status = 400;
        $order_detail = OrderDetail::where('id', $request->order_detail_id)->where('is_payment', 1)->first();

        if($order_detail) {
            $subject = "Pembayaran";
            $detail = "";

            if($action == "approved"){
                $order_detail->status_detail = 2;
                $order_detail->updated_by = $request->auth->id;
                $order_detail->save();

                if($order_detail){
                    $status = 200;
                    $detail = "Approved";
                    $message = "Approved for payment confirmation successfully";
                }
            }
            else if($action == "rejected"){
                $order_detail->status_detail = 98;
                $order_detail->is_payment = 0;
                $order_detail->updated_by = $request->auth->id;
                $order_detail->save();

                if($order_detail){
                    $status = 200;
                    $detail = "Rejected";
                    $message = "Rejected for payment confirmation successfully";
                }      
            }
            else{
                $order_detail->status_detail = 1;
                $order_detail->is_payment = 0;
                $order_detail->updated_by = $request->auth->id;
                $order_detail->save();

                if($order_detail){
                    $status = 200;
                    $detail = "Revision";
                    $message = "Revision for payment confirmation successfully";
                }    
            }

            $payload = array(
                "user_id" => $order_detail->order->customer_id,
                "subject" => $subject,
                "message" => $message,
                "order_no" => $order_detail->order_no
            );
            NotificationCOntroller::create($payload);
        }

        else{
            $status = 400;
            $message = "Order has not uploaded proof of payment";         
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $order_detail
        ], $status);
    }

    public function confirmationDocument($action, Request $request) {
        $status = 400;
        $order_detail = OrderDetail::where('id', $request->order_detail_id)->first();

        if($order_detail) {
            if($order_detail->status_detail == 2 || $order_detail->status_detail == 3){
                $subject = "Dokumen";
                $detail = "";

                if($action == "approved"){
                    $order_detail->status_detail = 4;
                    $order_detail->updated_by = $request->auth->id;
                    $order_detail->save();

                    if($order_detail){
                        $status = 200;
                        $detail = "Approved";
                        $message = "Approved for document confirmation successfully";
                    }
                }
                else if($action == "rejected"){
                    $order_detail->status_detail = 99;
                    $order_detail->is_document = 0;
                    $order_detail->updated_by = $request->auth->id;
                    $order_detail->save();

                    if($order_detail){
                        $status = 200;
                        $detail = "Rejected";
                        $message = "Rejected for document confirmation successfully";
                    }      
                }
                else{
                    $order_detail->status_detail = 3;
                    $order_detail->is_document = 0;
                    $order_detail->updated_by = $request->auth->id;
                    $order_detail->save();

                    if($order_detail){
                        $status = 200;
                        $detail = "Revision";
                        $message = "Revision for document confirmation successfully";
                    }      
                }

                $payload = array(
                    "user_id" => $order_detail->order->customer_id,
                    "subject" => $subject,
                    "message" => $message,
                    "order_no" => $order_detail->order_no
                );
                NotificationCOntroller::create($payload);
            }
            else{
                $status = 400;
                $message = "Order has not uploaded document";                      
            }
        }

        else{
            $status = 400;
            $message = "Order has not uploaded proof of document";         
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $order_detail
        ], $status);
    }


    public function assign(Request $request) {
		$order_detail = OrderDetail::where('id', $request->order_detail_id)->where('status_detail', 4)->first();
		
        if($order_detail){
            // $order_detail->status_detail = 1;
            $order_detail->status_detail = 5;
            $order_detail->updated_by = $request->auth->id;
            $order_detail->save();

            $order_driver_truck = new OrderDriverTruck;
            $order_driver_truck->order_detail_id = $request->order_detail_id;
            $order_driver_truck->driver_id = $request->driver_id;
            $order_driver_truck->truck_id = $request->truck_id;
            $order_driver_truck->save();

            if($order_detail && $order_driver_truck){
                $status = 200;
                $message = "Approved successfully";
            }

    		else{
    			$status = 400;
    			$message = "Action invalid";
    		}
        }

        else{
            $status = 400;
            $message = "Order has not uploaded document";   
        }
			
		return response()->json([
			"status" => $status,
			"message" => $message,
            "data" => $order_detail
		], 200);
    }
}