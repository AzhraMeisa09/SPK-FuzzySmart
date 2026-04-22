<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subkriteria extends Model
{
    use SoftDeletes;

    protected $table = 'subkriteria';

    protected $fillable = [
        'kriteria_id',
        'nama',
        'rubrik_mb',
        'rubrik_bsh',
        'rubrik_bsb',
    ];

    /**
     * Get dynamic Kode (C1.1, C1.2, etc)
     */
    public function getKodeAttribute(): string
    {
        // Calculate sequence within parent
        $index = self::where('kriteria_id', $this->kriteria_id)
            ->where('id', '<=', $this->id)
            ->count();
            
        return ($this->kriteria->kode ?? 'C?') . '.' . $index;
    }

    protected $dates = ['deleted_at'];

    // ================= RELASI =================

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function jadwalSubkriteria()
    {
        return $this->hasMany(JadwalSubkriteria::class, 'subkriteria_id');
    }

    public function templateRekomendasi()
    {
        return $this->hasMany(TemplateRekomendasi::class, 'subkriteria_id');
    }

    public function penilaian()
    {
        return $this->hasManyThrough(
            PenilaianMingguan::class,
            JadwalSubkriteria::class,
            'subkriteria_id',
            'jadwal_sub_id'
        );
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'subkriteria_id');
    }

    // ================= HELPER =================

    public function getRubrik(string $kategori): ?string
    {
        return match (strtoupper($kategori)) {
            'MB'  => $this->rubrik_mb,
            'BSH' => $this->rubrik_bsh,
            'BSB' => $this->rubrik_bsb,
            default => null,
        };
    }
}
