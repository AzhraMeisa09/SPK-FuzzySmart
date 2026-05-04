<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'kelas_id',
        'wali_murid_id',
        'kode',
        'nama',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_orang_tua',
        'alamat',
        'no_hp_orang_tua',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    // ========================
    // RELASI
    // ========================

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function wali()
    {
        return $this->belongsToMany(User::class, 'wali_siswa', 'siswa_id', 'user_id');
    }

    public function penilaian()
    {
        return $this->hasMany(PenilaianMingguan::class, 'siswa_id');
    }

    public function portofolio()
    {
        return $this->hasMany(Portofolio::class, 'siswa_id');
    }

    public function evaluasi()
    {
        return $this->hasMany(Evaluasi::class, 'siswa_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'siswa_id');
    }

    public function laporan()
    {
        return $this->hasMany(LaporanEvaluasi::class, 'siswa_id');
    }

    // ========================
    // HELPER (ADVANCED 🔥)
    // ========================
    public function evaluasiByPeriode($periodeId)
    {
        return $this->evaluasi()
                    ->where('periode_id', $periodeId)
                    ->first();
    }
}
