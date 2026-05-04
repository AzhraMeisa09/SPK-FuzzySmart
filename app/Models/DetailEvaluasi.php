<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailEvaluasi extends Model
{
    public $timestamps = false;

    protected $table = 'detail_evaluasi';

    protected $fillable = [
        'evaluasi_id',
        'subkriteria_id',
        'nilai_crisp',
        'nilai_normalisasi',
        'bobot_snapshot',
        'kategori',
        'rekomendasi_detail',
    ];

    protected $casts = [
        'nilai_crisp' => 'double',
    ];

    // ================= RELASI =================

    public function evaluasi()
    {
        return $this->belongsTo(Evaluasi::class, 'evaluasi_id');
    }

    public function subkriteria()
    {
        return $this->belongsTo(Subkriteria::class, 'subkriteria_id');
    }
}
