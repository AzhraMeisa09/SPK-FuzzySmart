<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kriteria';

    protected $fillable = [
        'nama',
        'bobot'
    ];

    /**
     * Get dynamic Kode (C1, C2, etc) based on ID
     */
    public function getKodeAttribute(): string
    {
        return 'C' . $this->id;
    }

    protected $casts = [
        'bobot' => 'double'
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
