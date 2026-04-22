<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelasGuru extends Model
{
    use HasFactory;

    protected $table = 'kelas_guru';

    protected $fillable = [
        'kelas_id',
        'guru_id'
    ];

    public $timestamps = false; // karena di migration tidak ada timestamps

    // ========================
    // RELASI
    // ========================

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
