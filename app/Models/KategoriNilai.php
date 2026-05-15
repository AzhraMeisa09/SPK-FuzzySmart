<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriNilai extends Model
{
    use HasFactory, HasCustomId;

    public $timestamps = false;

    protected $table = 'kategori_nilai';
    protected $primaryKey = 'id_kategori';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'KN';
    }

    protected $fillable = [
        'nama',
        'nilai_l',
        'nilai_m',
        'nilai_u',
        'nilai_crisp',
        'rentang_min',
        'rentang_max',
    ];

    protected $casts = [
        'nilai_l' => 'float',
        'nilai_m' => 'float',
        'nilai_u' => 'float',
        'nilai_crisp' => 'float',
        'rentang_min' => 'float',
        'rentang_max' => 'float',
    ];

    // ================= RELASI =================
    public function penilaian()
    {
        return $this->hasMany(PenilaianMingguan::class, 'kategori_id');
    }

    // ================= FUZZY =================

    // Hitung nilai crisp
    public function hitungCrisp(): float
    {
        return ($this->nilai_l + $this->nilai_m + $this->nilai_u) / 3;
    }

    // Auto isi nilai_crisp saat save
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->nilai_crisp = ($model->nilai_l + $model->nilai_m + $model->nilai_u) / 3;
        });
    }

    // ================= HELPER =================

    // Cari kategori berdasarkan nilai %
    public static function findByNilai(float $nilai): ?self
    {
        return static::where('rentang_min', '<=', $nilai)
            ->where('rentang_max', '>=', $nilai)
            ->orderBy('rentang_min')
            ->first();
    }

    // Shortcut kategori
    public static function mb()
    {
        return static::where('nama', 'MB')->first();
    }

    public static function bsh()
    {
        return static::where('nama', 'BSH')->first();
    }

    public static function bsb()
    {
        return static::where('nama', 'BSB')->first();
    }
}
