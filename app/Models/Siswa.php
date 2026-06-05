<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

use App\Traits\HasCustomId;

class Siswa extends Model
{
    use HasFactory, HasCustomId;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'S';
    }

    protected $fillable = [
        'id_siswa',
        'kelas_id',
        'wali_murid_id',
        'kode',
        'kode_registrasi',
        'name',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_orang_tua',
        'alamat',
        'no_hp_orang_tua',
        'foto',
    ];

    /**
     * Generate kode registrasi unik format TKP-XXXXX
     * Terdiri dari prefix TKP- + kombinasi 2 huruf, 1 angka, 1 huruf, 1 angka
     * Dipastikan unik di database.
     */
    public static function generateKodeRegistrasi(): string
    {
        do {
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $random = '';
            for ($i = 0; $i < 5; $i++) {
                $random .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $kode = 'TKP-' . $random;
        } while (static::where('kode_registrasi', $kode)->exists());

        return $kode;
    }

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
        return $this->belongsToMany(User::class, 'wali_siswa', 'siswa_id', 'user_id')->using(WaliSiswa::class);
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
