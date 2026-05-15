<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\HasCustomId;

class PeriodeKelas extends Pivot
{
    use HasFactory, HasCustomId;

    protected $table = 'periode_kelas';
    protected $primaryKey = 'id_periode_kelas';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'PKL';
    }

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
