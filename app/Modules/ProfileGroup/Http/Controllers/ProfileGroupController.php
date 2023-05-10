<?php

namespace App\Modules\ProfileGroup\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Department\Models\Department;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProfileGroupController extends Controller
{

/////////////

public function EquipmentsCheckedByProfileGroup($profile_groups_id){
    
    //$currentDate=Carbon::now();
    $currentDate=Carbon::now();
    if (("06:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"14:30:00")) {
        $timeFrom=$currentDate->format('Y-m-d'). " 06:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 14:30:00";
        return ProfileGroup::getEquipmentsCheckedByProfileGroup($timeFrom,$timeTo,$profile_groups_id);

        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }
    else if ("14:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"22:30:00") {
       
        $timeFrom=$currentDate->format('Y-m-d'). " 14:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 22:30:00";
        return ProfileGroup::getEquipmentsCheckedByProfileGroup($timeFrom,$timeTo,$profile_groups_id);

        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }
    else if ((("22:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"00:00:00")) || ("00:00:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"06:30:00")){
        $timeFrom=$currentDate->format('Y-m-d'). " 22:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 06:30:00";
        return ProfileGroup::getEquipmentsCheckedByProfileGroup($timeFrom,$timeTo,$profile_groups_id);
        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }

    
}



////////////





    public function index(){

        $profileGroups=ProfileGroup::with('department')->get();

        return [
            "payload" => $profileGroups,
            "status" => "200_00"
        ];
    }

    public function get($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $profileGroup->department=$profileGroup->department;
            return [
                "payload" => $profileGroup,
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

        $profileGroup=ProfileGroup::make($request->all());
        $profileGroup->save();
        $profileGroup->department=$profileGroup->department;
        return [
            "payload" => $profileGroup,
            "status" => "200"
        ];
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
            "department_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $profileGroup=ProfileGroup::find($request->id);
        if (!$profileGroup) {
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_3"
            ];
        }
        if($request->name!=$profileGroup->name){
            if(ProfileGroup::where("name",$request->name)->count()>0)
                return [
                    "payload" => "The profile group name has been already taken ! ",
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

        $profileGroup->name=$request->name;
        $profileGroup->department_id=$request->department_id;
        $profileGroup->save();
        $profileGroup->department=$profileGroup->department;
        return [
            "payload" => $profileGroup,
            "status" => "200"
        ];
    }

    public function delete(Request $request){
        $profileGroup=ProfileGroup::find($request->id);
        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $profileGroup->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }

    public function deleteUserFromProfileGroup(Request $request){
        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload" => "The searched profiel group row does not exist !",
                "status" => "404_4"
            ];
        }
        $user=User::find($request->user_id);
        if(!$user){
            return [
                "payload" => "The searched user row does not exist !",
                "status" => "404_4"
            ];
        }

            $profileGroup->users()->detach($user);
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];

    }

    public function addUserToProfileGroup(Request $request){
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "profile_group_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $user=User::find($request->user_id);
        if(!$user){
            return [
                "payload"=>"User is not exist !",
                "status"=>"user_404",
            ];
        }
        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload"=>"Profile Group is not exist !",
                "status"=>"profileGroup_404",
            ];
        }

        $profileGroup->users()->attach($user);
        $user->profileGroups=$user->profileGroups;
        return [
            "payload" => $user,
            "status" => "200"
        ];
    }

    public function getProfileGroupUsers($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $profileGroup->users()->with("fonction")->get(),
                "status" => "200_1"
            ];
        }
    }

    public function getProfileGroupEquipments($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $profileGroup->equipments,
                "status" => "200_1"
            ];
        }
    }

    public function getProfileGroupDamageTypes($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $profileGroup->damageTypes()->with("department")->get(),
                "status" => "200_1"
            ];
        }
    }

    public function getProfileGroupsByCounters(){
        $profileGroups=ProfileGroup::all();
        $counters=[];
        
        for ($i=0;$i<count($profileGroups);$i++){
            $equipment=$profileGroups[$i]->equipments;
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $functionalEquipmnet=0;
            $equipmentCheckedCount=0;
            if ($this->EquipmentsCheckedByProfileGroup($profileGroups[$i]->id) == null) {
                
                $equipmentCheckedCount=0;
            }else $equipmentCheckedCount=$this->EquipmentsCheckedByProfileGroup($profileGroups[$i]->id);

            for ($k=0;$k<count($equipment);$k++){
                $damages=$equipment[$k]->damages;
                $ThisIsDamaged=false;
                for ($j=0;$j<count($damages);$j++){
                    if($damages[$j]->status=="on progress"){
                        $damagedCount++;
                        $ThisIsDamaged=true;
                    }
                    else if($damages[$j]->status=="resolved"){
                        $confirmedCount++;
                        $ThisIsDamaged=true;
                    }
                    else if($damages[$j]->status=="closed"){
                        $closedCount++;
                    }
                   
                }
                if(!$ThisIsDamaged){
                    $functionalEquipmnet++;
                }
            }
            array_push($counters,[
                "id" => $profileGroups[$i]->id,
                "name" => $profileGroups[$i]->name,
                "equipmentCheckedCount" => $equipmentCheckedCount,
                "department_id" => $profileGroups[$i]->department->id,
                "equipmentsCount" => count($equipment),
                "functionalEquipmnet" => $functionalEquipmnet,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ]);
        }
        return [
            "payload" => $counters,
            "status" => "200_1"
        ];

    }

    public function getProfileGroupsByCountersIT(){
        $profileGroups=ProfileGroup::all();
        $counters=[];

        for ($i=0;$i<count($profileGroups);$i++){
            $equipment=$profileGroups[$i]->equipments;
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $functionalEquipmnet=0;
            for ($k=0;$k<count($equipment);$k++){
                $damages=$equipment[$k]->damages()->with("declaredBy.fonction.department")
                ->with("confirmedBy.fonction.department")
                ->with("closedBy.fonction.department")
                ->with("rejectedBy.fonction.department")
                ->with("equipment.profileGroup.department")
                ->with("damageType","damageType.profileGroup.department","damageType.department")
                ->get();
                $ThisIsDamaged=false;
                for ($j=0;$j<count($damages);$j++){
                    if ($damages[$j]->damageType->department->id==1) {
                        if($damages[$j]->status=="on progress"){
                            $damagedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="resolved"){
                            $confirmedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="closed"){
                            $closedCount++;
                        }
                    }

                }
                if(!$ThisIsDamaged){
                    $functionalEquipmnet++;
                }

            }
            array_push($counters,[
                "id" => $profileGroups[$i]->id,
                "name" => $profileGroups[$i]->name,
                "department_id" => $profileGroups[$i]->department->id,
                "equipmentsCount" => count($equipment),
                "functionalEquipmnet" => $functionalEquipmnet,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ]);
        }
        return [
            "payload" => $counters,
            "status" => "200_1"
        ];

    }

    public function getProfileGroupsByCountersTEC(){
        $profileGroups=ProfileGroup::all();
        $counters=[];

        for ($i=0;$i<count($profileGroups);$i++){
            $equipment=$profileGroups[$i]->equipments;
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $functionalEquipmnet=0;
            $equipmentChecked=0;

            for ($k=0;$k<count($equipment);$k++){
                $damages=$equipment[$k]->damages()->with("declaredBy.fonction.department")
                ->with("confirmedBy.fonction.department")
                ->with("closedBy.fonction.department")
                ->with("rejectedBy.fonction.department")
                ->with("equipment.profileGroup.department")
                ->with("damageType","damageType.profileGroup.department","damageType.department")
                ->get();
                $ThisIsDamaged=false;
                for ($j=0;$j<count($damages);$j++){
                    if ($damages[$j]->damageType->department->id==2) {
                        if($damages[$j]->status=="on progress"){
                            $damagedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="resolved"){
                            $confirmedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="closed"){
                            $closedCount++;
                        }
                    }

                }
                if(!$ThisIsDamaged){
                    $functionalEquipmnet++;
                }

            }
            array_push($counters,[
                "id" => $profileGroups[$i]->id,
                "name" => $profileGroups[$i]->name,
                "department_id" => $profileGroups[$i]->department->id,
                "equipmentsCount" => count($equipment),
                "functionalEquipmnet" => $functionalEquipmnet,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ]);
        }
        return [
            "payload" => $counters,
            "status" => "200_1"
        ];

    }

    public function getProfileGroupsByCounter($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $equipments=$profileGroup->equipments;
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $functionalEquipmnet=0;
            $nonFunctionalEquipmnet=0;

            for ($k=0;$k<count($equipments);$k++){
                $damages=$equipments[$k]->damages;
                $ThisIsDamaged=false;
                for ($j=0;$j<count($damages);$j++){
                    if($damages[$j]->status=="on progress"){
                        $damagedCount++;
                        $ThisIsDamaged=true;
                    }
                    else if($damages[$j]->status=="resolved"){
                        $confirmedCount++;
                        $ThisIsDamaged=true;
                    }
                    else if($damages[$j]->status=="closed"){
                        $closedCount++;
                    }
                }
                if(!$ThisIsDamaged){
                    $functionalEquipmnet++;
                }
            }
            $nonFunctionalEquipmnet=count($equipments)-$functionalEquipmnet;
            return [
                "payload" => [
                    "id" => $profileGroup->id,
                    "name" => $profileGroup->name,
                    "equipmentsCount" => count($equipments),
                    "functionalEquipmnet" => $functionalEquipmnet,
                    "department_id" => $profileGroup->department->id,
                    "damagedCount" => $damagedCount,
                    "confirmedCount" => $confirmedCount,
                    "closedCount" => $closedCount,
                    "nonFunctionalEquipmnet"=> $nonFunctionalEquipmnet,
                ],
                "status" => "200_1"
            ];
        }



    }


    public function getProfileGroupsByCounterIT($id){
        $profileGroup=ProfileGroup::find($id);

        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $equipments=$profileGroup->equipments;
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $functionalEquipmnet=0;
            $data=collect ([]);
            $nonFunctionalEquipmnet=0;
            for ($i=0; $i < count($equipments); $i++) {
                $damages=$equipments[$i]->damages()->with("declaredBy.fonction.department")
                ->with("confirmedBy.fonction.department")
                ->with("closedBy.fonction.department")
                ->with("rejectedBy.fonction.department")
                ->with("equipment.profileGroup.department")
                ->with("damageType","damageType.profileGroup.department","damageType.department")
                ->get();
                $ThisIsDamaged=false;
                for ($j=0;$j<count($damages);$j++){
                    $data->push($damages[$j]);
                    if ($damages[$j]->damageType->department->id==1) {
                        if($damages[$j]->status=="on progress"){
                            $damagedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="resolved"){
                            $confirmedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="closed"){
                            $closedCount++;
                        }
                    }

                }
                if(!$ThisIsDamaged){
                    $functionalEquipmnet++;
                }
            }
            $nonFunctionalEquipmnet=count($equipments)-$functionalEquipmnet;

            return [
                "payload" => [
                    "departmentIT" => "IT",
                    "id" => $profileGroup->id,
                    "name" => $profileGroup->name,
                    "equipmentsCount" => count($equipments),
                    "functionalEquipmnet" => $functionalEquipmnet,
                    "department_id" => $profileGroup->department->id,
                    "damagedCount" => $damagedCount,
                    "confirmedCount" => $confirmedCount,
                    "closedCount" => $closedCount,
                    "nonFunctionalEquipmnet"=> $nonFunctionalEquipmnet,

                ],
                "status" => "200_1"
            ];
        }



    }

    public function getProfileGroupsByCounterTEC($id){
        $profileGroup=ProfileGroup::find($id);

        if(!$profileGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $equipments=$profileGroup->equipments;
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $functionalEquipmnet=0;
            $data=collect ([]);
            $nonFunctionalEquipmnet=0;

            for ($i=0; $i < count($equipments); $i++) {
                $damages=$equipments[$i]->damages()->with("declaredBy.fonction.department")
                ->with("confirmedBy.fonction.department")
                ->with("closedBy.fonction.department")
                ->with("rejectedBy.fonction.department")
                ->with("equipment.profileGroup.department")
                ->with("damageType","damageType.profileGroup.department","damageType.department")
                ->get();
                $ThisIsDamaged=false;
                for ($j=0;$j<count($damages);$j++){
                    $data->push($damages[$j]);
                    if ($damages[$j]->damageType->department->id==2) {
                        if($damages[$j]->status=="on progress"){
                            $damagedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="resolved"){
                            $confirmedCount++;
                            $ThisIsDamaged=true;
                        }
                        else if($damages[$j]->status=="closed"){
                            $closedCount++;
                        }
                    }

                }
                if(!$ThisIsDamaged){
                    $functionalEquipmnet++;
                }
            }
            $nonFunctionalEquipmnet=count($equipments)-$functionalEquipmnet;

            return [
                "payload" => [
                    "departmentIT" => "TEC",
                    "id" => $profileGroup->id,
                    "name" => $profileGroup->name,
                    "equipmentsCount" => count($equipments),
                    "functionalEquipmnet" => $functionalEquipmnet,
                    "department_id" => $profileGroup->department->id,
                    "damagedCount" => $damagedCount,
                    "confirmedCount" => $confirmedCount,
                    "closedCount" => $closedCount,
                    "nonFunctionalEquipmnet"=> $nonFunctionalEquipmnet,

                ],
                "status" => "200_1"
            ];
        }



    }

}
