<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_letter_id', 
        'tujuan_head', 
        'status_disposisi', 
        'file_hasil_disposisi', 
        'diteruskan_ke_bagian'
    ];

    public function incomingLetter()
    {
        return $this->belongsTo(IncomingLetter::class);
    }
}
