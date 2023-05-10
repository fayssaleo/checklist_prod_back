<?php

namespace App\Modules\Damage\Models;

use App\Modules\DamageType\Models\DamageType;
use App\Modules\Department\Models\Department;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Damage extends Model
{
    use HasFactory;
    protected $guarded=["id"];

    public function declaredBy(){
        return $this->belongsTo(User::class,"declaredBy_id");
    }

    public function confirmedBy(){
        return $this->belongsTo(User::class,"confirmedBy_id");
    }

    public function closedBy(){
        return $this->belongsTo(User::class,"closedBy_id");
    }
    public function driverOut(){
        return $this->belongsTo(User::class,"driverOut");
    }
    public function rejectedBy(){
        return $this->belongsTo(User::class,"rejectedBy_id");
    }

    public function equipment(){
        return $this->belongsTo(Equipment::class);
    }

    public function damageType(){
        return $this->belongsTo(DamageType::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
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
