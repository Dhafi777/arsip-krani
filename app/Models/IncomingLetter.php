<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Impor SoftDeletes

class IncomingLetter extends Model
{
    use HasFactory, SoftDeletes; // 2. Panggil di dalam class

    protected $fillable = [
        'no_agenda', 'no_surat', 'tgl_surat', 'tgl_masuk', 
        'jenis_surat', 'pengirim', 'perihal', 'file_surat', 
        'status_surat', 'status_disposisi', 'catatan_admin', 'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function disposition()
    {
        return $this->hasOne(Disposition::class);
    }
}