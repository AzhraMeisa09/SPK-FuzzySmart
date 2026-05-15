<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluasi extends Model
{
    use HasFactory, HasCustomId;
    public $timestamps = false;

    protected $table = 'evaluasi';
    protected $primaryKey = 'id_evaluasi';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'E';
    }

    protected $fillable = [
        'periode_id',
        'siswa_id',
        'template_umum_id',
        'nilai_akhir',
        'kategori_akhir',
        'rekomendasi',
        'catatan_guru',
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

    // 🔥 Ambil nilai per subkriteria
    public function nilaiPerSubkriteria()
    {
        return $this->detail()->with('subkriteria.kriteria')->get();
    }
}
