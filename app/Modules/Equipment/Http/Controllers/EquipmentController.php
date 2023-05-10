<?php

namespace App\Modules\Equipment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EquipmentController extends Controller
{


/////////

/* public function getActualShift(){
    $thisDate = date("2022-02-16T06:30:00");
    $nowDate = Carbon::now();
    $shift = ["D", "A", "B", "C"];
    $momentDate = moment($thisDate);
    
    while ($momentDate.add(72, "hours").toDate() < $nowDate) {
      $shift = this.shiftArrays($shift);
    }

    if (( ($nowDate.getHours()==6 && $nowDate.getMinutes() >= 30) || ($nowDate.getHours() >= 7 ) ) &&
     ( ($nowDate.getHours()==14 && $nowDate.getMinutes() < 30) || ($nowDate.getHours() < 15 ) )) return $shift[0];

    else if (( ($nowDate.getHours()==14 && $nowDate.getMinutes() >= 30) || ($nowDate.getHours() >= 15 ) ) &&
     ( ($nowDate.getHours()==22 && $nowDate.getMinutes() < 30) || ($nowDate.getHours() < 23 ) ))
      return $shift[1];
    else if (
       ($nowDate.getHours()==22 && $nowDate.getMinutes() >= 30) ||
      ($nowDate.getHours() >= 0 && ($nowDate.getHours() < 6 && $nowDate.getMinutes() < 30))
    )
      return $shift[2];
    
}

public function shiftArrays($array){
    $c = "";
    $arrayC = array();
    $arrayC[3] = $arrayC[2];
    $arrayC[2] = $arrayC[1];
    $arrayC[1] = $arrayC[0];
    $arrayC[0] = $c;

    return $arrayC;
    
} */

public function EquipmentsCheckedByEquipment($equipment_id){
    
    //$currentDate=Carbon::now();
    $currentDate=Carbon::now();
    if (("06:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"14:30:00")) {
        $timeFrom=$currentDate->format('Y-m-d'). " 06:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 14:30:00";
        return ProfileGroup::getEquipmentsCheckedByEquipment($timeFrom,$timeTo,$equipment_id);

        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }
    else if ("14:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"22:30:00") {
       
        $timeFrom=$currentDate->format('Y-m-d'). " 14:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 22:30:00";
        return ProfileGroup::getEquipmentsCheckedByEquipment($timeFrom,$timeTo,$equipment_id);

        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }
    else if ((("22:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"00:00:00")) || ("00:00:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"06:30:00")){
        $timeFrom=$currentDate->format('Y-m-d'). " 22:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 06:30:00";
        return ProfileGroup::getEquipmentsCheckedByEquipment($timeFrom,$timeTo,$equipment_id);
        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }

    
}

public function EquipmentsCheckedByProfileGroup($profile_groups_id){
    
    //$currentDate=Carbon::now();
    $currentDate=Carbon::now();
    if (("06:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"14:30:00")) {
        $timeFrom=$currentDate->format('Y-m-d'). " 06:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 14:30:00";
        return ProfileGroup::getEquipmentsCheckedByEquipment($timeFrom,$timeTo,$profile_groups_id);

        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }
    else if ("14:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"22:30:00") {
       
        $timeFrom=$currentDate->format('Y-m-d'). " 14:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 22:30:00";
        return ProfileGroup::getEquipmentsCheckedByEquipment($timeFrom,$timeTo,$profile_groups_id);

        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }
    else if ((("22:30:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"00:00:00")) || ("00:00:00"<$currentDate->format('H:i:s') && $currentDate->format('H:i:s')<"06:30:00")){
        $timeFrom=$currentDate->format('Y-m-d'). " 22:30:00";
        $timeTo=$currentDate->format('Y-m-d') . " 06:30:00";
        return ProfileGroup::getEquipmentsCheckedByEquipment($timeFrom,$timeTo,$profile_groups_id);
        return [
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,

        ];
    }

    
}
/////////




    public function index(){

        $equipment=Equipment::with('profileGroup.department')->get();

        return [
            "payload" => $equipment,
            "status" => "200_00"
        ];
    }

    public function get($id){
        $equipment=Equipment::find($id);
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $equipment->profileGroup=$equipment->profileGroup;
            $equipment->profileGroup->department=$equipment->profileGroup->department;
            return [
                "payload" => $equipment,
                "status" => "200_1"
            ];
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required|string|unique:equipments,name",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload"=>"profile group is not exist !",
                "status"=>"profile_group_404",
            ];
        }
        $equipment=Equipment::make($request->all());
        $equipment->save();
        $equipment->profileGroup=$equipment->profileGroup;
        $equipment->profileGroup->department=$equipment->profileGroup->department;

        return [
            "payload" => $equipment,
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
        $equipment=Equipment::find($request->id);
        if (!$equipment) {
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_3"
            ];
        }


        $profileGroup=ProfileGroup::find($request->profile_group_id);
        if(!$profileGroup){
            return [
                "payload"=>"profile group is not exist !",
                "status"=>"profile_group_404",
            ];
        }

        if($request->name!=$equipment->name){
            if(Equipment::where("name",$request->name)->count()>0)
                return [
                    "payload" => "The department has been already taken ! ",
                    "status" => "406_2"
                ];
        }

        $equipment->name=$request->name;

        $equipment->save();
        $equipment->profileGroup=$equipment->profileGroup;
        $equipment->profileGroup->department=$equipment->profileGroup->department;

        return [
            "payload" => $equipment,
            "status" => "200"
        ];
    }

    public function delete(Request $request){
        $equipment=Equipment::find($request->id);
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            $equipment->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }


    public function getEquipmentsByCounters($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched profiel group row does not exist !",
                "status" => "404_4"
            ];
        }

        $equipments=$profileGroup->equipments;

        $counters=[];
        for ($i=0;$i<count($equipments);$i++){
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $ceriticalDefectsCount=0;
            $isShecked=false;

            $damages=$equipments[$i]->damages;
            for ($k=0;$k<count($damages);$k++){
                if($damages[$k]->status=="on progress"){
                    $damagedCount++;
                }
                if($damages[$k]->status=="resolved"){
                    $confirmedCount++;
                }
                if($damages[$k]->status=="closed"){
                    $closedCount++;
                }
                if ($damages[$k]->damageType->important ==1) {
                    $ceriticalDefectsCount++;
                }
                $test=$this->EquipmentsCheckedByEquipment($damages[$k]->equipment->id);
                    if ($test >=1) {
                        $isShecked=true;
                    }
            }
            array_push($counters,[
                "id" => $equipments[$i]->id,
                "nameEquipment" => $equipments[$i]->name,
                "isShecked" => $isShecked,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
                "ceriticalDefectsCount" => $ceriticalDefectsCount,
            ]);
        }


        return [
            "payload" => $counters,
            "status" => "200_1"
        ];

    }
    public function getEquipmentsByCountersIT($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched profiel group row does not exist !",
                "status" => "404_4"
            ];
        }

        $equipments=$profileGroup->equipments;

        $counters=[];
        for ($i=0;$i<count($equipments);$i++){
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $ceriticalDefectsCount=0;

            $damages=$equipments[$i]->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            ->get();
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
                    if ($damages[$j]->damageType->important ==1) {
                        $ceriticalDefectsCount++;
                    }
                }
            }
            array_push($counters,[
                "id" => $equipments[$i]->id,
                "nameEquipment" => $equipments[$i]->name,
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

    public function getEquipmentsByCountersTEC($id){
        $profileGroup=ProfileGroup::find($id);
        if(!$profileGroup){
            return [
                "payload" => "The searched profiel group row does not exist !",
                "status" => "404_4"
            ];
        }

        $equipments=$profileGroup->equipments;

        $counters=[];
        for ($i=0;$i<count($equipments);$i++){
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $ceriticalDefectsCount=0;
            $isShecked=false;

            $damages=$equipments[$i]->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            ->get();
            for ($j=0;$j<count($damages);$j++){
                if ($damages[$j]->damageType->department->id==2) {
                    if($damages[$j]->status=="on progress"){
                        $damagedCount++;
                    }
                    else if($damages[$j]->status=="resolved"){
                        $confirmedCount++;
                    }
                    else if($damages[$j]->status=="closed"){
                        $closedCount++;
                    }
                    if ($damages[$j]->damageType->important ==1) {
                        $ceriticalDefectsCount++;
                    }
                    
                    
                }
            }
            array_push($counters,[
                "id" => $equipments[$i]->id,
                "nameEquipment" => $equipments[$i]->name,
                "isShecked" => $isShecked,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
                "ceriticalDefectsCount" => $ceriticalDefectsCount,

            ]);
        }


        return [
            "payload" => $counters,
            "status" => "200_1"
        ];

    }
    public function getEquipmentsByCounter($id){
        $equipment=Equipment::find($id);
        if(!$equipment){
            return [
                "payload" => "The searched equipment row does not exist !",
                "status" => "404_4"
            ];
        }

            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $damages=$equipment->damages;
            for ($k=0;$k<count($damages);$k++){
                if($damages[$k]->status=="on progress"){
                    $damagedCount++;
                }
                if($damages[$k]->status=="resolved"){
                    $confirmedCount++;
                }
                if($damages[$k]->status=="closed"){
                    $closedCount++;
                }
            }



        return [
            "payload" => [
                "id" => $equipment->id,
                "nameEquipment" => $equipment->name,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ],
            "status" => "200_1"
        ];

    }

    public function getEquipmentsByCounterIT($id){
        $equipment=Equipment::find($id);
        if(!$equipment){
            return [
                "payload" => "The searched equipment row does not exist !",
                "status" => "404_4"
            ];
        }

            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $damages=$equipment->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            ->get();
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



        return [
            "payload" => [
                "id" => $equipment->id,
                "nameEquipment" => $equipment->name,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ],
            "status" => "200_1"
        ];

    }
    public function getEquipmentsByCounterTEC($id){
        $equipment=Equipment::find($id);
        if(!$equipment){
            return [
                "payload" => "The searched equipment row does not exist !",
                "status" => "404_4"
            ];
        }

            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $damages=$equipment->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            ->get();
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



        return [
            "payload" => [
                "id" => $equipment->id,
                "nameEquipment" => $equipment->name,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
            ],
            "status" => "200_1"
        ];

    }
}
