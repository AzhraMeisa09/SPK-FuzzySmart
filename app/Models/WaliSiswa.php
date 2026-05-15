<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasCustomId;

class WaliSiswa extends Pivot
{
    use HasCustomId;

    protected $table = 'wali_siswa';
    protected $primaryKey = 'id_wali_siswa';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'WS';
    }

    protected $fillable = [
        'user_id',
        'siswa_id'
    ];

    public $timestamps = true;
}
