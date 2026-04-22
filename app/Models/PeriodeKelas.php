<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeriodeKelas extends Pivot
{
    use HasFactory;

    protected $table = 'periode_kelas';

    protected $fillable = [
        'periode_id',
        'kelas_id'
    ];

    public $timestamps = true;

    // ================= RELASI =================

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
