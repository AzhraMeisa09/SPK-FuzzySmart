<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasCustomId;

class Subkriteria extends Model
{
    use SoftDeletes, HasCustomId;

    protected $table = 'subkriteria';
    protected $primaryKey = 'id_subkriteria';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return ''; // Handled by trait logic for subkriteria
    }

    protected $fillable = [
        'kriteria_id',
        'nama_subkriteria',
        'rubrik_mb',
        'rubrik_bsh',
        'rubrik_bsb',
    ];

    protected $dates = ['deleted_at'];

    // ================= RELASI =================

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'id_kriteria');
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
