<?php

namespace App\Modules\Damage\Http\Controllers;
use \stdClass;
use App\Http\Controllers\Controller;
use App\Libs\UploadTrait;
use App\Modules\Damage\Models\Damage;
use App\Modules\Damage\Models\Photo;
use App\Modules\DamageType\Models\DamageType;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use App\Modules\PresenceCheck\Models\PresenceCheck;

use App\Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DamageController extends Controller
{
    use UploadTrait;

    public function index(){

        $damages=Damage::with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            ->get();

        return [
            "payload" => $damages,
            "status" => "200_00"
        ];
    }
    //declareDamage
    public function declareDamage(Request $request){
        $declaredDamages=[];
        for ($i=0;$i<count($request->damages);$i++){
            $declaredBy=User::find($request->damages[$i]["declaredBy_id"]);
            if(!$declaredBy){
                return [
                    "payload"=>"user is not exist !",
                    "status"=>"user_404",
                ];
            }

            $damageType=DamageType::find($request->damages[$i]["damage_type_id"]);
            if(!$damageType){
                return [
                    "payload"=>"damage type is not exist !",
                    "status"=>"damage_type_404",
                ];
            }

            $equipment=Equipment::find($request->damages[$i]["equipment_id"]);
            if(!$equipment){
                return [
                    "payload"=>"equipment is not exist !",
                    "status"=>"equipment_404",
                ];
            }
            $existDamage=Damage::select()
                ->where([
                    ['damage_type_id', '=', $request->damages[$i]["damage_type_id"]],
                    ['equipment_id', '=', $request->damages[$i]["equipment_id"]],
                    ['status', '=', "resolved"],
                ])
                ->orWhere([
                    ['damage_type_id', '=', $request->damages[$i]["damage_type_id"]],
                    ['equipment_id', '=', $request->damages[$i]["equipment_id"]],
                    ['status', '=', "on progress"],
                ])
                ->first();
            if($existDamage){
                return [
                    "payload"=>"The ".$damageType->name." damage is already ".$existDamage->status." !",
                    "status"=>"400",
                ];
            }
            $damage=Damage::make($request->damages[$i]);
            $damage->declaredAt=Carbon::now();
            $damage->shift=$request->damages[$i]['shift'];
            $damage->driverIn=$request->damages[$i]['declaredBy_id'];
             $driverOut=PresenceCheck::select()
                ->where([
                    ['user_id', '!=', $request->damages[$i]['declaredBy_id']],
                    ['equipment_id', '=', $request->damages[$i]["equipment_id"]],
                ])->orderBy('created_at', 'desc')
                ->first();
                if ($driverOut==null) {
                    $damage->driverOut=null;
                }else {
                    $damage->driverOut=$driverOut->user_id;
                }
            $damage->save();
            $damage->declaredBy=$damage->declaredBy()->with("fonction.department")->first();
            $damage->confirmedBy=$damage->confirmedBy()->with("fonction.department")->first();
            $damage->closedBy=$damage->closedBy()->with("fonction.department")->first();
            $damage->driver_out=$damage->driverOut()->with("fonction.department")->first();
            $damage->rejectedBy=$damage->rejectedBy()->with("fonction.department")->first();
            $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
            $damage->damageType=$damage->damageType()->with("profileGroup.department")->with("department")->first();
            
            array_push($declaredDamages,$damage);
        }


        return [
            "driverOut" => $damage->driverOut()->with("fonction.department")->first(),
            "payload" => $declaredDamages,
            "status" => "200"
        ];
    }

    public function confirmDamage(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
            "confirmedBy_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $confirmedBy=User::find($request->confirmedBy_id);
        if(!$confirmedBy){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }


        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }


        if($damage->status!="on progress"){
            return [
                "payload"=>"damage is not on progress to be resolved !",
                "status"=>"damage_400",
            ];

        }

        $damage->status="resolved";
        $damage->confirmedBy_id=$confirmedBy->id;
        $damage->resolveDescription=$request->resolveDescription;
        $damage->confirmedAt=Carbon::now();
        $damage->save();
        $damage->declared_by=$damage->declaredBy()->with("fonction.department")->first();
        $damage->confirmed_by=$damage->confirmedBy()->with("fonction.department")->first();
        $damage->closed_by=$damage->closedBy()->with("fonction.department")->first();
        $damage->driver_out=$damage->driverOut()->with("fonction.department")->first();
        $damage->rejected_by=$damage->rejectedBy()->with("fonction.department")->first();
        $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
        $damage->damage_type=$damage->damageType()->with("profileGroup.department")->with("department")->first();
        
        return [
            "payload" => $damage,
            "status" => "200"
        ];
    }

    public function closeDamage(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
            "closedBy_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $closedBy=User::find($request->closedBy_id);
        if(!$closedBy){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }


        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }

       /*  if($damage->status!="resolved"){
            return [
                "payload"=>"damage is not resolved to be closed !",
                "status"=>"damage_400",
            ];
        } */

        $damage->status="closed";
        $damage->closedBy_id=$closedBy->id;
        $damage->closedAt=Carbon::now();
        $damage->save();
        $damage->declared_by=$damage->declaredBy()->with("fonction.department")->first();
        $damage->confirmed_by=$damage->confirmedBy()->with("fonction.department")->first();
        $damage->closed_by=$damage->closedBy()->with("fonction.department")->first();
        $damage->driver_out=$damage->driverOut()->with("fonction.department")->first();
        $damage->rejected_by=$damage->rejectedBy()->with("fonction.department")->first();
        $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
        $damage->damage_type=$damage->damageType()->with("profileGroup.department")->with("department")->first();
        
        return [
            "payload" => $damage,
            "status" => "200"
        ];
    }

    public function revertDamage(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => "required",
            "rejectedBy_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                "payload" => $validator->errors(),
                "status" => "406_2"
            ];
        }
        $rejectedBy=User::find($request->rejectedBy_id);
        if(!$rejectedBy){
            return [
                "payload"=>"user is not exist !",
                "status"=>"user_404",
            ];
        }


        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload"=>"damage is not exist !",
                "status"=>"damage_404",
            ];
        }

        if($damage->status!="resolved"){
            return [
                "payload"=>"damage is not resolved to be rejected !",
                "status"=>"damage_400",
            ];
        }

        $damage->status="on progress";
        $damage->rejectedBy_id=$rejectedBy->id;
        $damage->rejectedAt=Carbon::now();
        $damage->rejectedTimes=++$damage->rejectedTimes;
        $damage->rejectedDescription=$request->rejectedDescription;
        $damage->save();
        $damage->declared_by=$damage->declaredBy()->with("fonction.department")->first();
        $damage->confirmed_by=$damage->confirmedBy()->with("fonction.department")->first();
        $damage->closed_by=$damage->closedBy()->with("fonction.department")->first();
        $damage->driver_out=$damage->driverOut()->with("fonction.department")->first();
        $damage->rejected_by=$damage->rejectedBy()->with("fonction.department")->first();
        $damage->equipment=$damage->equipment()->with("profileGroup.department")->first();
        $damage->damage_type=$damage->damageType()->with("profileGroup.department")->with("department")->first();
        
        return [
            "payload" => $damage,
            "status" => "200"
        ];
    }

    public function getDamagesByProfileGroup($id){
        $profielGroup=ProfileGroup::select()->where('id', $id)->with("equipments")->first();
        if(!$profielGroup){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }


        else {
            $damages=[];
            for ($x = 0; $x < count($profielGroup->equipments); $x++) {
                $thisDamages=$profielGroup->equipments[$x]->damages()
                    ->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("rejectedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    
                    ->get()
                    ->toArray();
                $damages=array_merge($damages,$thisDamages);
            }
            return [
                "payload" => $damages,
                "status" => "200_1"
            ];
        }
    }

    public function getDamagesByEquipments($id){
        $equipment=Equipment::select()->where('id', $id)->with("profileGroup")->first();
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $data = $equipment->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("driverOut.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("driverOut.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            
            ->get();
            return [
                "payload" => $data,
                "status" => "200_1"
            ];
        }
    }
    public function getDamagesByEquipmentsRapport($id){
        $equipment=Equipment::select()->where('id', $id)->with("profileGroup")->first();
        $rapportdata=collect ([]);

        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {

            $data = $equipment->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("driverOut.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            
            ->get();

            for ($i=0; $i < count($data) ; $i++) {
                    $rapportModel = new stdClass();
                    $rapportModel->name=$equipment->name;
                    $rapportModel->status=$data[$i]->status;
                    $rapportModel->Description=$data[$i]->description;
                    $rapportModel->rejectedTimes=$data[$i]->rejectedTimes;
                    $rapportModel->declared_by=$data[$i]->declaredBy->username==null?"":$data[$i]->declaredBy->username;
                    $rapportModel->declaredAt=$data[$i]->declaredAt;
                    $rapportModel->damage_type=$data[$i]->damageType->name;
                    $rapportModel->damage_type_departement=$data[$i]->damageType->department->name;
                    $rapportdata->push($rapportModel);
            }





            return [
                "payload" => $rapportdata,
                "status" => "200_1"
            ];
        }
    }
    public function getDamagesByEquipmentsIT($id){
        $equipment=Equipment::select()->where('id', $id)->with("profileGroup")->first();
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $data = $equipment->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("driverOut.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            
            ->get();
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $dataIT=collect ([]);
            $nameEquipment=$equipment->name;
            for ($i=0; $i <count($data) ; $i++) {
                if (count($data) == 0) {
                } else {
                    if ($data[$i]->damageType->department->id==1) {
                        $dataIT->push($data[$i]);
                        if($data[$i]->status=="on progress"){
                            $damagedCount++;
                        }
                        else if($data[$i]->status=="resolved"){
                            $confirmedCount++;
                        }
                        else if($data[$i]->status=="closed"){
                            $closedCount++;
                                      }

                  }
                }
            }
            return [
                "payload" => $dataIT,
                "nameEquipment" => $nameEquipment,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
                "status" => "200_1"
            ];
        }
    }

    public function getDamagesByEquipmentsTEC($id){
        $equipment=Equipment::select()->where('id', $id)->with("profileGroup")->first();
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $data = $equipment->damages()->with("declaredBy.fonction.department")
            ->with("confirmedBy.fonction.department")
            ->with("closedBy.fonction.department")
            ->with("driverOut.fonction.department")
            ->with("rejectedBy.fonction.department")
            ->with("equipment.profileGroup.department")
            ->with("damageType","damageType.profileGroup.department","damageType.department")
            
            ->get();
            $damagedCount=0;
            $confirmedCount=0;
            $closedCount=0;
            $dataTEC=collect ([]);
            $nameEquipment=$equipment->name;
            for ($i=0; $i <count($data) ; $i++) {
                if (count($data) == 0) {

                }else{
                   if ($data[$i]->damageType->department->id==2) {
                    $dataTEC->push($data[$i]);
                    if($data[$i]->status=="on progress"){
                        $damagedCount++;
                    }
                    else if($data[$i]->status=="resolved"){
                        $confirmedCount++;
                    }
                    else if($data[$i]->status=="closed"){
                        $closedCount++;
                    }
                }
                }

            }
            return [
                "payload" => $dataTEC,
                "nameEquipment" => $nameEquipment,
                "damagedCount" => $damagedCount,
                "confirmedCount" => $confirmedCount,
                "closedCount" => $closedCount,
                "status" => "200_1"
            ];
        }
    }

    public function getDamagesByDeclareds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->declaredBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("rejectedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getDamagesByConfirmeds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->confirmedBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("rejectedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getDamagesByCloseds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->closedBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("rejectedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getDamagesByrejecteds($id){
        $declaredBy=User::select()->where('id', $id)->with("fonction")->first();
        if(!$declaredBy){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            return [
                "payload" => $declaredBy->rejectedBys()->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("rejectedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    
                    ->get(),
                "status" => "200_1f"
            ];
        }
    }

    public function getEquipmentDamagesMergedWithDamageTypes($id){
        $equipment=Equipment::select()->where('id', $id)->with("profileGroup")->first();
        if(!$equipment){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_1"
            ];
        }
        else {
            $damaeTypeWithDamages=[];
            $profileGroupDamageTypes=$equipment->profileGroup->damageTypes()->get();

            for ($i=0;$i<count($profileGroupDamageTypes);$i++){
                $profileGroupDamageTypes[$i]->damage=Damage::select()
                    ->where([
                        ['damage_type_id', '=', $profileGroupDamageTypes[$i]->id],
                        ['equipment_id', '=', $equipment->id],
                        ['status', '=', "resolved"],
                    ])
                    ->orWhere([
                        ['damage_type_id', '=', $profileGroupDamageTypes[$i]->id],
                        ['equipment_id', '=', $equipment->id],
                        ['status', '=', "on progress"],
                    ])
                    ->with("declaredBy.fonction.department")
                    ->with("confirmedBy.fonction.department")
                    ->with("closedBy.fonction.department")
                    ->with("rejectedBy.fonction.department")
                    ->with("equipment.profileGroup.department")
                    ->with("damageType","damageType.profileGroup.department","damageType.department")
                    
                    ->first();
                $profileGroupDamageTypes[$i]->department=$profileGroupDamageTypes[$i]->department;
                array_push($damaeTypeWithDamages,$profileGroupDamageTypes[$i]);
            }
            return [
                "payload" => $damaeTypeWithDamages,
                "status" => "200_1"
            ];
        }
    }

    


    public function delete(Request $request){
        $damage=Damage::find($request->id);
        if(!$damage){
            return [
                "payload" => "The searched row does not exist !",
                "status" => "404_4"
            ];
        }
        else {
            if(!$damage->status=="resolved"){
                return [
                    "payload" => "This damage cannot be removed the status is 'resolved' !",
                    "status" => "400_4"
                ];
            }
            $damage->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => "200_4"
            ];
        }
    }

}
