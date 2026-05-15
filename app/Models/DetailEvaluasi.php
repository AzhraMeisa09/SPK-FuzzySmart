<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailEvaluasi extends Model
{
    use HasFactory, HasCustomId;
    public $timestamps = false;

    protected $table = 'detail_evaluasi';
    protected $primaryKey = 'id_detail_evaluasi';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'DE';
    }

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
