<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'description'];

    // Relasi ke tabel User (Siapa yang melakukan aksi)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}