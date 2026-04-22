<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    protected $table = 'rekomendasi';

    protected $fillable = [
        'evaluasi_id',
        'siswa_id',
        'subkriteria_id',
        'template_id',
        'kategori_hasil',
        'catatan_guru',
    ];

    // ================= RELASI =================

    public function evaluasi()
    {
        return $this->belongsTo(Evaluasi::class, 'evaluasi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function subkriteria()
    {
        return $this->belongsTo(Subkriteria::class, 'subkriteria_id');
    }

    public function template()
    {
        return $this->belongsTo(TemplateRekomendasi::class, 'template_id');
    }

    // ================= HELPER =================

    // 🔥 Ambil isi rekomendasi (prioritas: catatan guru > template)
    public function isiFinal(): string
    {
        return $this->catatan_guru ?: $this->template?->isi ?? '-';
    }

    // 🔥 Label kategori
    public function kategoriLabel(): string
    {
        return $this->kategori_hasil;
    }
}
