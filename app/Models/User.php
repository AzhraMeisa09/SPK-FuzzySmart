<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasCustomId;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasCustomId;

    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'U';
    }

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
        return $this->belongsToMany(Kelas::class, 'kelas_guru', 'guru_id', 'kelas_id')->using(KelasGuru::class);
    }

    // guru memberi penilaian
    public function penilaian()
    {
        return $this->hasMany(PenilaianMingguan::class, 'guru_id');
    }

    // wali murid punya siswa
    public function siswaWali()
    {
        return $this->belongsToMany(Siswa::class, 'wali_siswa', 'user_id', 'siswa_id')->using(WaliSiswa::class);
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
