<?php

namespace App\Modules\Equipment\Models;

use App\Modules\Damage\Models\Damage;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\PresenceCheck\Models\PresenceCheck;

class Equipment extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    protected $table="equipments";

    public function profileGroup()
    {
        return $this->belongsTo(ProfileGroup::class);
    }
    public function presenceCheck()
    {
        return $this->hasOne(PresenceCheck::class);
    }

    public function damages()
    {
        return $this->hasMany(Damage::class);
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
