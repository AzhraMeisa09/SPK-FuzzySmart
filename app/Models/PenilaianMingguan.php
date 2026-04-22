<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianMingguan extends Model
{
    protected $table = 'penilaian_mingguan';

    protected $fillable = [
        'jadwal_sub_id',
        'siswa_id',
        'guru_id',
        'kategori_id',
        'nilai_l',
        'nilai_m',
        'nilai_u',
        'nilai_crisp',
        'catatan',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ================= RELASI =================

    public function jadwalSubkriteria()
    {
        return $this->belongsTo(JadwalSubkriteria::class, 'jadwal_sub_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriNilai::class, 'kategori_id');
    }

    // ================= RELASI TURUNAN =================

    public function subkriteria()
    {
        return $this->jadwalSubkriteria?->subkriteria ?? null;
    }

    public function minggu()
    {
        return $this->jadwalSubkriteria?->minggu ?? null;
    }

    // ================= HELPER =================

    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function bolehDiubah(): bool
    {
        return $this->isDraft();
    }

    // 🔥 nilai crisp (Mengutamakan nilai fisik denormalisasi)
    public function nilaiCrisp(): float
    {
        return $this->nilai_crisp ?? ($this->kategori?->nilai_crisp ?? 0);
    }

    // 🔥 nilai fuzzy (Mengutamakan nilai fisik denormalisasi)
    public function nilaiFuzzy(): array
    {
        return [
            'l' => $this->nilai_l ?? ($this->kategori?->nilai_l ?? 0),
            'm' => $this->nilai_m ?? ($this->kategori?->nilai_m ?? 0),
            'u' => $this->nilai_u ?? ($this->kategori?->nilai_u ?? 0),
        ];
    }

    // 🔥 label kategori
    public function labelKategori(): string
    {
        return $this->kategori?->nama ?? '-';
    }
}
