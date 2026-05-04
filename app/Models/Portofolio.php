<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portofolio extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $table = 'portofolio';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'minggu_id',
        'judul',
        'deskripsi',
    ];

    // ================= RELASI =================

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function minggu()
    {
        return $this->belongsTo(MingguPenilaian::class, 'minggu_id');
    }

    public function images()
    {
        return $this->hasMany(PortofolioImage::class, 'portofolio_id');
    }

    // ================= HELPER =================

    // Ambil cover (foto pertama)
    public function cover()
    {
        return $this->images()->first();
    }

    // Cek apakah ada foto
    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    // Ambil semua path gambar
    public function imagePaths()
    {
        return $this->images()->pluck('file_path');
    }
}
