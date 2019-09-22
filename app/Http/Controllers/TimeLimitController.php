<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\TimeLimit;
use DB;

class TimeLimitController extends Controller
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
       $time_limits = TimeLimit::where('setting_type', 1)->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $time_limits
        ], 200);   
    }

    public function detail(Request $request) {
        $time_limit = TimeLimit::where('id', $request->setting_id)->first();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $time_limit
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required|unique:setting_apps,setting_type',
            'time' => 'required|numeric'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create time limit failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $time_limit = TimeLimit::create([
            'setting_type' => $request->type,
            'setting_value' => $request->time,
            'created_by' => $request->auth->id
        ]);

        return response()->json([
            "status" => 200,
            "message" => "Time limit created successfully",
            "data" => $time_limit
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'unique:setting_apps,setting_type,'.$request->setting_id,
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Update time_limit failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $time_limit = TimeLimit::find($request->setting_id);

        $time_limit->setting_type = $request->type;
        $time_limit->setting_value = $request->time;
        $time_limit->updated_by = $request->auth->id;
        $time_limit->update();


        return response()->json([
           "status" => 200,
            "message" => "Time limit updated successfully",
            "data" => $time_limit
            ], 200);        
    }

    public function delete(Request $request) {
        $time_limit = TimeLimit::find($request->setting_id);

        if($time_limit){
            $time_limit->deleted_by = $request->auth->id;
            $time_limit->save();
            $time_limit->delete();
            $status = 200;
            $message = "Time limit deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a time limit failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $time_limit
        ], $status);        
    }

    public function trash()
    {
        $time_limit = TimeLimit::onlyTrashed()->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $time_limit
        ], 200);       
    }
}
