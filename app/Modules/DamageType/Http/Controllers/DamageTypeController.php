<?php

namespace App\Modules\DamageType\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DamageType\Models\DamageType;
use App\Modules\Department\Models\Department;
use App\Modules\Fonction\Models\Fonction;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DamageTypeController extends Controller
{

    public function index(){

        $damageTypes=DamageType::with('profileGroup')->with('department')->get();

        return [
            "payload" => $damageTypes,
            "status" => "200_00"
        ];
    }

    public function get($id){
        $damageType=DamageType::find($id);
        if(!$damageType){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $damageType->department=$damageType->department;
            $damageType->profileGroup=$damageType->profileGroup;
            return [
                "payload" => $damageType,
                "status" => "200_1"
            ];
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required|string|unique:damage_types,name",
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

        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload"=>"profile group is not exist !",
                "status"=>"profile_group_404",
            ];
        }

        $damageType=DamageType::make($request->all());
       // $damageType->important=$damageType->important;
        $damageType->save();
        $damageType->department=$damageType->department;
        $damageType->profileGroup=$damageType->profileGroup;
        return [
            "payload" => $damageType,
            "status" => "200_01"
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
        $damageType=DamageType::find($request->id);
        if (!$damageType) {
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_3"
            ];
        }
        if($request->name!=$damageType->name){
            if(DamageType::where("name",$request->name)->count()>0)
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
        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload"=>"profile group is not exist !",
                "status"=>"profile_group_404",
            ];
        }


        $damageType->name=$request->name;
        $damageType->department_id=$request->department_id;
        $damageType->profile_group_id=$request->profile_group_id;

        $damageType->save();
        $damageType->department=$damageType->department;
        $damageType->profileGroup=$damageType->profileGroup;
        return [
            "payload" => $damageType,
            "status" => "200"
        ];
    }

    public function delete(Request $request){
        $damageType=DamageType::find($request->id);
        if(!$damageType){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $damageType->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }
}
