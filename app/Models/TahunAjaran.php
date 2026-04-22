<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_aktif'
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ========================
    // RELASI
    // ========================

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'tahun_ajaran_id');
    }

    public function periodePenilaian()
    {
        return $this->hasMany(PeriodePenilaian::class, 'tahun_ajaran_id');
    }

    // ========================
    // BUSINESS RULE
    // ========================
    // Pastikan hanya 1 tahun ajaran aktif
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->is_aktif) {
                static::where('id', '!=', $model->id)
                      ->update(['is_aktif' => false]);
            }
        });
    }

    // ========================
    // HELPER
    // ========================
    public static function getAktif()
    {
        return self::where('is_aktif', true)->first();
    }
}
