<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplateRekomendasiUmum extends Model
{
    use HasFactory, HasCustomId;
    protected $table = 'template_rekomendasi_umum';
    protected $primaryKey = 'id_template_umum';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'TRU';
    }

    protected $fillable = [
        'kategori',
        'isi',
        'prioritas',
    ];
}
