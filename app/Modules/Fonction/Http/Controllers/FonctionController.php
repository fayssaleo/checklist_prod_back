<?php

namespace App\Modules\Fonction\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Department\Models\Department;
use App\Modules\Fonction\Models\Fonction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FonctionController extends Controller
{

    public function index(){

        $fonctions=Fonction::with('department')->get();

        return [
            "payload" => $fonctions,
            "status" => "200_00"
        ];
    }

    public function get($id){
        $fonction=Fonction::find($id);
        if(!$fonction){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $fonction->department=$fonction->department;
            return [
                "payload" => $fonction,
                "status" => "200_1"
            ];
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required|string|unique:fonctions,name",
            "department_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $department=Department::find($request->department_id);
        if(!$department){
            return [
                "payload"=>"department is not exist !",
                "status"=>"department_404",
            ];
        }
        $fonction=Fonction::make($request->all());
        $fonction->save();
        $fonction->department=$fonction->department;
        return [
            "payload" => $fonction,
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
        $fonction=Fonction::find($request->id);
        if (!$fonction) {
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_3"
            ];
        }
        if($request->name!=$fonction->name){
            if(Fonction::where("name",$request->name)->count()>0)
                return [
                    "payload" => "The fonction has been already taken ! ",
                    "status" => "406_2"
                ];
        }
        $department=Department::find($request->department_id);
        if(!$department){
            return [
                "payload"=>"department is not exist !",
                "status"=>"department_404",
            ];
        }
        $fonction->name=$request->name;
        $fonction->department_id=$request->department_id;

        $fonction->save();
        $fonction->department=$fonction->department;
        return [
            "payload" => $fonction,
            "status" => "200"
        ];
    }

    public function delete(Request $request){
        $fonction=Fonction::find($request->id);
        if(!$fonction){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $fonction->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }
}
