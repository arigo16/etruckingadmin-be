<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\OrderRefundExport;
use App\OrderRefundImport;
use App\OrderRefund;
  
class OrderRefundController extends Controller
{
    /**
    * @return \Illuminate\Support\Collection
    */   
    /**
    * @return \Illuminate\Support\Collection
    */

    public function listRefund($req_status){
        if($req_status == "pending")
            $status = 0;
        else if($req_status == "ongoing")
            $status = 1;
        else
            $status = 2;

        $refund = OrderRefund::with('order.order_detail', 'order.location_pickup', 'order.location_delivery', 'order.customer.accounts', 'driver')->where('status', $status)->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $refund
        ], 200);
    }

    public function updateAmount(Request $request){
        $refund = OrderRefund::where('id', $request->order_refund_id)->where('status', 0)->first();
        if(!$refund)
            return response()->json([
                "status" => 400,
                "message" => "Data not found or already refund",
                "data" => $refund
            ], 400);

        $refund->status = 1;
        $refund->amount = $request->amount;
        $refund->updated_by = $request->auth->id;
        $refund->save();

        return response()->json([
            "status" => 200,
            "message" => "Updated amount successfully!",
            "data" => $refund
        ], 200);
    }

    public function refund(Request $request){
        $refund = OrderRefund::where('id', $request->order_refund_id)->where('status', 1)->first();
        if(!$refund)
            return response()->json([
                "status" => 400,
                "message" => "Data not found or already refund",
                "data" => $refund
            ], 400);

        $refund->status = 2;
        $refund->updated_by = $request->auth->id;
        $refund->save();

        return response()->json([
            "status" => 200,
            "message" => "Refund successfully!",
            "data" => $refund
        ], 200);
    }
}