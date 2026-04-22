<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    public $timestamps = false;

    protected $table = 'evaluasi';

    protected $fillable = [
        'periode_id',
        'siswa_id',
        'nilai_akhir',
        'kategori_akhir',
        'is_final',
    ];

    protected $casts = [
        'nilai_akhir' => 'double',
        'is_final'    => 'boolean',
    ];

    // ================= RELASI =================

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailEvaluasi::class, 'evaluasi_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'evaluasi_id');
    }

    public function laporan()
    {
        return $this->hasOne(LaporanEvaluasi::class, 'evaluasi_id');
    }

    // ================= HELPER =================

    public function isFinal(): bool
    {
        return $this->is_final;
    }

    // 🔥 Tentukan kategori akhir otomatis
    public function tentukanKategori(): string
    {
        $kategori = KategoriNilai::findByNilai($this->nilai_akhir);
        return $kategori?->nama ?? '-';
    }

    // 🔥 Ambil nilai per kriteria
    public function nilaiPerKriteria()
    {
        return $this->detail()->with('kriteria')->get();
    }
}
