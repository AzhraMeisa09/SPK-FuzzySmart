<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateRekomendasiUmum extends Model
{
    protected $table = 'template_rekomendasi_umum';

    protected $fillable = [
        'kategori',
        'isi',
        'prioritas',
    ];
}
