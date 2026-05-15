<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplateRekomendasi extends Model
{
    use HasFactory, HasCustomId;
    protected $table = 'template_rekomendasi';
    protected $primaryKey = 'id_template';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'TR';
    }

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
