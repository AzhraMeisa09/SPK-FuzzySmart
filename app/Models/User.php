<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Field yang boleh diisi (sesuai ERD)
     */
    protected $fillable = [
        'nama_lengkap',
        'username',
        'email',
        'password',
        'role',
        'foto_profil',
        'no_hp',
        'alamat',
        'is_active'
    ];

    /**
     * Field yang disembunyikan
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting data
     */
    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // =========================
    // RELASI
    // =========================

    // guru mengajar kelas
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_guru', 'guru_id', 'kelas_id');
    }

    // guru memberi penilaian
    public function penilaian()
    {
        return $this->hasMany(PenilaianMingguan::class, 'guru_id');
    }

    // wali murid punya siswa
    public function siswaWali()
    {
        return $this->hasMany(Siswa::class, 'wali_murid_id');
    }

    // guru upload portofolio
    public function portofolio()
    {
        return $this->hasMany(Portofolio::class, 'guru_id');
    }

    // admin membuat periode
    public function periode()
    {
        return $this->hasMany(PeriodePenilaian::class, 'created_by');
    }
}
