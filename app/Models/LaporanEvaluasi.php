<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanEvaluasi extends Model
{
    use HasFactory, HasCustomId;
    public $timestamps = false;

    protected $table = 'laporan_evaluasi';
    protected $primaryKey = 'id_laporan';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'L';
    }

    protected $fillable = [
        'evaluasi_id',
        'siswa_id',
        'kelas_id',
        'tahun_ajaran_id',
        'semester',
        'file_path',
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

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function getUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    // ================= HELPER =================

    // 🔥 Ambil nama file saja
    public function fileName(): string
    {
        return basename($this->file_path);
    }

    // 🔥 Cek apakah file sudah ada
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }
}
