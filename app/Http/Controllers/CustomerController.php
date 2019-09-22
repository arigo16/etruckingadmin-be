<?php

namespace App\Http\Controllers;

use Validator;
use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
       $customers = Customer::with('user_aliases')->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $customers
        ], 200);   
    }

    public function detail(Request $request) {
        $customer = Customer::with('user_aliases')->where('id', $request->customer_id)->first();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $customer
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create customer failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $customer = Customer::create([
            'full_name' => $request->full_name,
            'company_name' => $request->company_name,
            'images' => $request->images,
            'created_by' => $request->auth->id
        ]);

        if($customer){
            return response()->json([
               "status" => 200,
                "message" => "customer created successfully",
                "data" => $customer
                ], 200);
        }

        else{
            return response()->json([
               "status" => 401,
                "message" => "customer failed to create successfully",
                "data" => $customer
                ], 401);
        }
    }

    public function update(Request $request) {
       $status = $message = "";

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Update customer failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $customer = Customer::find($request->customer_id);

        if($customer){
            $customer->full_name = $request->full_name;
            $customer->company_name = $request->company_name;
            $customer->images = $request->images;
            $customer->updated_by = $request->auth->id;
            $customer->update();
            
            $status = 200;
            $message = "customer updated successfully";
        }

        else{
            $status = 410;
            $message = "customer not found";
        }
        

        return response()->json([
           "status" => $status,
            "message" => $message,
            "data" => $customer
            ], $status);        
    }

    public function delete(Request $request) {
        $customer = Customer::find($request->customer_id);

        if($customer){
            $customer->deleted_by = $request->auth->id;
            $customer->save();
            $customer->delete();
            $status = 200;
            $message = "customer deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a customer failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $customer
        ], $status);        
    }

    public function trash()
    {
        $customer = Customer::onlyTrashed()->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $customer
        ], 200);       
    }
}
