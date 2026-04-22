<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateRekomendasi extends Model
{
    protected $table = 'template_rekomendasi';

    protected $fillable = [
        'subkriteria_id',
        'kategori',
        'isi',
        'prioritas',
    ];

    // ================= RELASI =================

    public function subkriteria()
    {
        return $this->belongsTo(Subkriteria::class, 'subkriteria_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'template_id');
    }

    // ================= HELPER =================

    // 🔥 Ambil template berdasarkan subkriteria + kategori
    public static function findTemplate($subkriteriaId, $kategori)
    {
        return static::where('subkriteria_id', $subkriteriaId)
            ->where('kategori', $kategori)
            ->first();
    }
}
