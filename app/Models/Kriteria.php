<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasCustomId;

class Kriteria extends Model
{
    use HasFactory, SoftDeletes, HasCustomId;

    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'C';
    }

    protected $fillable = [
        'nama_kriteria',
        'bobot_kriteria',
        'is_aktif'
    ];

    protected $casts = [
        'bobot_kriteria' => 'double',
        'is_aktif' => 'boolean'
    ];

    // ========================
    // RELASI
    // ========================

    // Kriteria → Subkriteria
    public function subkriteria()
    {
        return $this->hasMany(Subkriteria::class, 'kriteria_id');
    }

    // Kriteria → Detail Evaluasi (hasil perhitungan)
    public function detailEvaluasi()
    {
        return $this->hasMany(DetailEvaluasi::class, 'kriteria_id');
    }
}
