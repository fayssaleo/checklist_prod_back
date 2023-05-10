<?php

namespace App\Modules\Comment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\User\Models\User;
use App\Modules\Damage\Models\Damage;
use App\Modules\Comment\Models\Photo;

class Comment extends Model
{
    use HasFactory;
    protected $guarded=["id"];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function damage(){
        return $this->belongsTo(Damage::class);
    }
    public function photos(){
        return $this->hasMany(Photo::class);
    }

    protected $casts = [
        
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',
    ];
}
