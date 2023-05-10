<?php

namespace App\Modules\User\Models;

use App\Modules\Damage\Models\Damage;
use App\Modules\Fonction\Models\Fonction;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Modules\PresenceCheck\Models\PresenceCheck;

class User extends Authenticatable
{
    use  Notifiable, HasApiTokens ,HasFactory;

    protected $guarded=["id"];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    protected $hidden = [
        'password',
    ];

    public function fonction()
    {
        return $this->belongsTo(Fonction::class);
    }

    public function presenceCheck()
    {
        return $this->hasOne(PresenceCheck::class);
    }

    public function profileGroups(){
        return $this->belongsToMany(ProfileGroup::class,"user_profile_group")
            ->withTimestamps();
    }

    public function declaredBys()
    {
        return $this->hasMany(Damage::class,"declaredBy_id");
    }

    public function confirmedBys()
    {
        return $this->hasMany(Damage::class,"confirmedBy_id");
    }

    public function closedBys()
    {
        return $this->hasMany(Damage::class,"closedBy_id");
    }

    public function rejectedBys()
    {
        return $this->hasMany(Damage::class,"rejectedBy_id");
    }
    public function driverOut()
    {
        return $this->hasMany(Damage::class,"driverOut");
    }
    protected $casts = [
        'declaredAt' => 'datetime:d/m/Y H:i',
        'confirmedAt' => 'datetime:d/m/Y H:i',
        'closedAt' => 'datetime:d/m/Y H:i',
        'rejectedAt' => 'datetime:d/m/Y H:i',
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',
        'dateLogin' => 'datetime:Y/m/d H:i',

    ];

}
