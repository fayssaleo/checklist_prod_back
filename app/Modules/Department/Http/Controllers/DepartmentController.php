<?php

namespace App\Modules\Department\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Department\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{

    public function index(){

        $departments=Department::all();

        return [
            "payload" => $departments,
            "status" => "200_00"
        ];
    }

    public function get($id){
        $department=Department::find($id);
        if(!$department){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $department,
                "status" => "200_1"
            ];
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required|string|unique:departments,name",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $department=Department::make($request->all());
        $department->save();
        return [
            "payload" => $department,
            "status" => "200"
        ];
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $department=Department::find($request->id);
        if (!$department) {
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_3"
            ];
        }
        if($request->name!=$department->name){
            if(Department::where("name",$request->name)->count()>0)
                return [
                    "payload" => "The department has been already taken ! ",
                    "status" => "406_2"
                ];
        }
        $department->name=$request->name;

        $department->save();
        return [
            "payload" => $department,
            "status" => "200"
        ];
    }

    public function delete(Request $request){
        $department=Department::find($request->id);
        if(!$department){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $department->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }

}
