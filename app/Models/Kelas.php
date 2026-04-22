<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_kelas'
    ];

    public $timestamps = false;

    // ========================
    // RELASI
    // ========================

    // Kelas → Tahun Ajaran (Many to One)
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // Kelas → Guru (Many to Many via kelas_guru)
    public function guru()
    {
        return $this->belongsToMany(
            User::class,
            'kelas_guru',
            'kelas_id',
            'guru_id'
        );
    }

    // Kelas → Siswa (One to Many)
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    // Kelas → Periode Penilaian (Many to Many via periode_kelas)
    public function periode()
    {
        return $this->belongsToMany(
            PeriodePenilaian::class,
            'periode_kelas',
            'kelas_id',
            'periode_id'
        )->using(PeriodeKelas::class)
         ->withTimestamps();
    }
}
