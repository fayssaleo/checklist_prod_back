<?php

namespace App\Modules\ProfileGroup\Models;

use App\Modules\DamageType\Models\DamageType;
use App\Modules\Department\Models\Department;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileGroup extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function users(){
        return $this->belongsToMany(User::class,"user_profile_group")
            ->withTimestamps();
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function damageTypes()
    {
        return $this->hasMany(DamageType::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }
    public static function getEquipmentsChecked($from,$to){
        return ProfileGroup::select('profile_groups.name as profile_group', 'equipments.name as equipment','presence_checks.created_at as checked_at','users.username')
            ->join('equipments',"equipments.profile_group_id","=","profile_groups.id")
            ->join('presence_checks',"equipments.id","=","presence_checks.equipment_id")
            ->join('users',"users.id","=","presence_checks.user_id")
            ->whereBetween('presence_checks.created_at', [$from, $to])
            ->get();
    }
    public static function getEquipmentsCheckedByProfileGroup($from,$to,$profile_groups_id){
        return ProfileGroup::select('profile_groups.name as profile_group', 'equipments.name as equipment','users.username')
            ->join('equipments',"equipments.profile_group_id","=","profile_groups.id")
            ->join('presence_checks',"equipments.id","=","presence_checks.equipment_id")
            ->join('users',"users.id","=","presence_checks.user_id")
            ->whereBetween('presence_checks.created_at', [$from, $to])
            ->where('profile_groups.id', $profile_groups_id)
            ->distinct('equipments.name')
            ->count();

           // ->get();
    }
    public static function getEquipmentsCheckedByEquipment($from,$to,$equipment_id){
        return ProfileGroup::select('profile_groups.name as profile_group', 'equipments.name as equipment','presence_checks.created_at as checked_at','users.username')
            ->join('equipments',"equipments.profile_group_id","=","profile_groups.id")
            ->join('presence_checks',"equipments.id","=","presence_checks.equipment_id")
            ->join('users',"users.id","=","presence_checks.user_id")
            ->whereBetween('presence_checks.created_at', [$from, $to])
            ->where('equipments.id', $equipment_id)
            ->count();
            //->get();
    }
    protected $casts = [
        'declaredAt' => 'datetime:d/m/Y H:i',
        'confirmedAt' => 'datetime:d/m/Y H:i',
        'closedAt' => 'datetime:d/m/Y H:i',
        'rejectedAt' => 'datetime:d/m/Y H:i',
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',

    ];

}
