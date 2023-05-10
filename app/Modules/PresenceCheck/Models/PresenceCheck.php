<?php

namespace App\Modules\PresenceCheck\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\User\Models\User;
use App\Modules\Equipment\Models\Equipment;

class PresenceCheck extends Model
{
    use HasFactory;
    protected $guarded=["id"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
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
