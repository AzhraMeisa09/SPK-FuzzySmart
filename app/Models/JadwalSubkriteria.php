<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

use App\Traits\HasCustomId;

class JadwalSubkriteria extends Pivot
{
    use HasCustomId;
    public $timestamps = true;

    protected $table = 'jadwal_subkriteria';
    protected $primaryKey = 'id_jadwal_sub';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'JS';
    }

    protected $fillable = [
        'minggu_id',
        'subkriteria_id',
        'urutan',
        'wajib'
    ];

    protected $casts = [
        'urutan' => 'integer',
        'wajib'  => 'boolean',
    ];

    // ================= RELASI =================

    public function minggu()
    {
        return $this->belongsTo(MingguPenilaian::class, 'minggu_id');
    }

    public function subkriteria()
    {
        return $this->belongsTo(Subkriteria::class, 'subkriteria_id');
    }

    public function penilaian()
    {
        return $this->hasMany(PenilaianMingguan::class, 'jadwal_sub_id');
    }

    // ================= HELPER =================

    // Ambil semua penilaian untuk 1 minggu ini
    public function penilaianLengkap()
    {
        return $this->penilaian()->with('siswa', 'kategori')->get();
    }

    // Cek apakah semua siswa sudah dinilai
    public function semuaSiswaSudahDinilai(): bool
    {
        $totalSiswa = $this->minggu
            ->periode
            ->kelas
            ->siswa()
            ->count();

        $sudahDinilai = $this->penilaian()->count();

        return $totalSiswa === $sudahDinilai;
    }

    // Ambil nilai rata-rata crisp untuk subkriteria ini
    public function rataNilaiCrisp(): float
    {
        return $this->penilaian()
            ->join('kategori_nilai', 'penilaian_mingguan.kategori_id', '=', 'kategori_nilai.id_kategori')
            ->avg('kategori_nilai.nilai_crisp') ?? 0;
    }
}
