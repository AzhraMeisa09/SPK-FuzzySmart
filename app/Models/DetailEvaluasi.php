<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailEvaluasi extends Model
{
    public $timestamps = false;

    protected $table = 'detail_evaluasi';

    protected $fillable = [
        'evaluasi_id',
        'kriteria_id',
        'nilai',
        'bobot_snapshot',
    ];

    protected $casts = [
        'nilai'           => 'double',
        'bobot_snapshot'  => 'double',
    ];

    // ================= RELASI =================

    public function evaluasi()
    {
        return $this->belongsTo(Evaluasi::class, 'evaluasi_id');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    // ================= HELPER =================

    // 🔥 Nilai setelah dikali bobot
    public function nilaiTerbobot(): float
    {
        return $this->nilai * $this->bobot_snapshot;
    }
}
