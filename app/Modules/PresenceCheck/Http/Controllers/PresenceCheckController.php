<?php

namespace App\Modules\PresenceCheck\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Modules\PresenceCheck\Models\PresenceCheck;

class PresenceCheckController extends Controller
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $presenceCheck=PresenceCheck::with('user',"user.fonction","user.fonction.department","user.profileGroups")
        ->with('equipment',"equipment.profileGroup","equipment.profileGroup.department")
        ->get();

        return [
            "payload" => $presenceCheck,
            "status" => "200_00"
        ];
    }
    public function get($id){
        $presenceCheck=PresenceCheck::find($id);
        if(!$presenceCheck){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $presenceCheck->user=$presenceCheck->user;
            $presenceCheck->user->profileGroups=$presenceCheck->user->profileGroups;
            $presenceCheck->user->fonction=$presenceCheck->user->fonction;
            $presenceCheck->user->fonction->department=$presenceCheck->user->fonction->department;

            $presenceCheck->equipment=$presenceCheck->equipment;
            $presenceCheck->equipment->profile_group=$presenceCheck->equipment->profileGroup;
            $presenceCheck->equipment->profile_group->department=$presenceCheck->equipment->profileGroup->department;

            return [
                "payload" => $presenceCheck,
                "status" => "200_1"
            ];
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "user_id" => "required:presence_checks,user_id",
            "equipment_id" => "required:presence_checks,equipment_id",

        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }

        $presenceCheckc=PresenceCheck::query()
            ->latest('id')
            ->first();

            if ($presenceCheckc!=null) {
                if ($presenceCheckc->user_id==$request->user_id && $presenceCheckc->equipment_id==$request->equipment_id) {
                    return [
                        "payload" => "Test !",
                        "status" => "404_1"
                    ];
                }else {
                    $presenceCheck=PresenceCheck::make($request->all());
                    $presenceCheck->save();
                    return [
                        "payload" => $presenceCheck,
                        "presenceCheckc" => $presenceCheckc,
                        "status" => "200"
                    ];
                }
            }
       else {
            $presenceCheck=PresenceCheck::make($request->all());
            $presenceCheck->save();
            return [
                "payload" => $presenceCheck,
                "presenceCheckc" => $presenceCheckc,
                "status" => "200"
            ];
        }


    }
    public function delete(Request $request){
        $presenceCheck=PresenceCheck::find($request->id);
        if(!$presenceCheck){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $presenceCheck->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }
}
