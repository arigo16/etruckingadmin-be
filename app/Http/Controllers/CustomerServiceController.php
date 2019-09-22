<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\CustomerServiceSetting;
use App\CustomerService;
use DB;

class CustomerServiceController extends Controller
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
       $setting = CustomerServiceSetting::where('setting_type', 2)->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $setting
        ], 200);   
    }

    public function detail(Request $request) {
        $setting = CustomerServiceSetting::where('setting_type', $request->setting_type)->first();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $setting
        ], 200);  
    }

    public function create(Request $request) {
        $setting = CustomerServiceSetting::where('setting_type', 2)->first();
        if($setting)
            return response()->json([
                "status" => 400,
                "message" => "Customer service already exists",
                "data" => $setting
            ]);

        $validator = Validator::make($request->all(), [
            'type' => 'required|unique:setting_apps,setting_type',
            'phone' => 'required'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create customer service failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $setting = CustomerServiceSetting::create([
            'setting_type' => $request->type,
            'setting_value' => $request->phone,
            'created_by' => $request->auth->id
        ]);

        return response()->json([
            "status" => 200,
            "message" => "Customer service created successfully",
            "data" => $setting
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'unique:setting_apps,setting_type,'.$request->setting_id,
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Update setting failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $setting = CustomerServiceSetting::find($request->setting_id);

        $setting->setting_type = $request->type;
        $setting->setting_value = $request->phone;
        $setting->updated_by = $request->auth->id;
        $setting->update();


        return response()->json([
           "status" => 200,
            "message" => "Customer service updated successfully",
            "data" => $setting
            ], 200);        
    }

    public function delete(Request $request) {
        $setting = CustomerServiceSetting::find($request->setting_id);

        if($setting){
            $setting->deleted_by = $request->auth->id;
            $setting->save();
            $setting->delete();
            $status = 200;
            $message = "Customer service deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a customer service failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $setting
        ], $status);        
    }

    public function trash()
    {
        $setting = CustomerServiceSetting::onlyTrashed()->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $setting
        ], 200);       
    }
}
