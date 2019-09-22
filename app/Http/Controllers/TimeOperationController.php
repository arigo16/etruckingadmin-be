<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\TimeOperation;
use DB;

class TimeOperationController extends Controller
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
        $time_operations = TimeOperation::whereIn('setting_type', [3,4])->get();
        foreach ($time_operations as $key => $time_operation) {
            $name = "";
            if($time_operation->setting_type == 3){
                $name = "Import";
            }
            else if($time_operation->setting_type == 4){
                $name = "Export";
            }
            $time_operation->setting_type_name = $name;
        }
        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $time_operations
        ], 200);   
    }

    public function detail(Request $request) {
        $time_operation = TimeOperation::where('setting_type', $request->setting_type)->first();
        if($request->setting_type == 3)
            $time_operation->setting_type_name = "Import";
        else if($request->setting_type == 4)
            $time_operation->setting_type_name = "Export";

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $time_operation
        ], 200);  
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required|unique:setting_apps,setting_type',
            'time' => 'required'
        ]);

        if($validator->fails()){
            return array(
                "status" => 401,
                "message" => "Create Time operation failed",
                "data" => array(
                    "error" => array_map(function($values) {
                                    return join(',', $values);
                                }, array_values($validator->errors()->toArray()))
                )
            );
        }

        $time_operation = TimeOperation::create([
            'setting_type' => $request->type,
            'setting_value' => $request->time,
            'created_by' => $request->auth->id
        ]);

        if($request->type == 3)
            $time_operation->setting_type_name = "Import";
        else if($request->type == 4)
            $time_operation->setting_type_name = "Export";

        return response()->json([
            "status" => 200,
            "message" => "Time operation created successfully",
            "data" => $time_operation
        ]);
    }

    public function update(Request $request) {
        $time_operation = TimeOperation::where('setting_type', $request->type)->first();

        $time_operation->setting_type = $request->type;
        $time_operation->setting_value = $request->time;
        $time_operation->updated_by = $request->auth->id;
        $time_operation->update();

        if($request->type == 3)
            $time_operation->setting_type_name = "Import";
        else if($request->type == 4)
            $time_operation->setting_type_name = "Export";

        return response()->json([
           "status" => 200,
            "message" => "Time operation updated successfully",
            "data" => $time_operation
            ], 200);        
    }

    public function delete(Request $request) {
        $time_operation = TimeOperation::find($request->setting_type);

        if($time_operation){
            $time_operation->deleted_by = $request->auth->id;
            $time_operation->save();
            $time_operation->delete();
            $status = 200;
            $message = "Time operation deleted successfully";
        }

        else{
            $status = 401;
            $message = "Delete a Time operation failed";
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $time_operation
        ], $status);        
    }

    public function trash()
    {
        $time_operation = TimeOperation::onlyTrashed()->get();

        return response()->json([
            "status" => 200,
            "message" => "Get data successfully",
            "data" => $time_operation
        ], 200);       
    }
}
