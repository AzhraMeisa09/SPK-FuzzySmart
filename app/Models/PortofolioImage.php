<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortofolioImage extends Model
{
    public $timestamps = false;

    protected $table = 'portofolio_images';

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
