<?php

namespace App\Modules\Comment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Comment\Models\Comment;

class Photo extends Model
{
    use HasFactory;
    protected $guarded=["id"];

    public function Comment(){
        return $this->belongTo(Comment::class);
    }

    protected $casts = [
        
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',
    ];
}
