<?php

namespace App\Modules\Department\Models;

use App\Modules\DamageType\Models\DamageType;
use App\Modules\Fonction\Models\Fonction;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $guarded=["id"];

    public function fonctions()
    {
        return $this->hasMany(Fonction::class);
    }
    
    public function damageTypes()
    {
        return $this->hasMany(DamageType::class);
    }

    public function profileGroups()
    {
        return $this->hasMany(ProfileGroup::class);
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
