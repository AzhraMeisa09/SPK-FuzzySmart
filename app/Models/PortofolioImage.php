<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PortofolioImage extends Model
{
    use HasFactory, HasCustomId;
    public $timestamps = false;

    protected $table = 'portofolio_images';
    protected $primaryKey = 'id_portofolio_images';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'PFI';
    }

    protected $fillable = [
        'portofolio_id',
        'file_path',
    ];

    public function portofolio()
    {
        return $this->belongsTo(Portofolio::class, 'portofolio_id');
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
