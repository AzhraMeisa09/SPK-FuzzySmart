<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluasi extends Model
{
    use HasFactory, HasCustomId;
    public $timestamps = false;

    protected $table = 'evaluasi';
    protected $primaryKey = 'id_evaluasi';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'E';
    }

    const STATUS_MENUNGGU_REVIEW = 'menunggu_review';
    const STATUS_DISETUJUI_GURU  = 'disetujui_guru';

    protected $fillable = [
        'periode_id',
        'siswa_id',
        'template_umum_id',
        'nilai_akhir',
        'kategori_akhir',
        'kategori_rekomendasi_sistem',
        'kategori_keputusan_guru',
        'rekomendasi',
        'catatan_guru',
        'is_final',
        'status_validasi',
        'tanggal_validasi',
        'id_guru_validator',
    ];

    protected $casts = [
        'nilai_akhir'      => 'double',
        'is_final'         => 'boolean',
        'tanggal_validasi' => 'datetime',
    ];

    // ================= RELASI =================

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailEvaluasi::class, 'evaluasi_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'evaluasi_id');
    }

    public function laporan()
    {
        return $this->hasOne(LaporanEvaluasi::class, 'evaluasi_id');
    }

    public function guruValidator()
    {
        return $this->belongsTo(User::class, 'id_guru_validator', 'id_user');
    }

    // ================= HELPER =================

    public function isFinal(): bool
    {
        return $this->is_final;
    }

    public function isValidatedByGuru(): bool
    {
        return $this->status_validasi === self::STATUS_DISETUJUI_GURU;
    }

    public function isMenungguReview(): bool
    {
        return $this->status_validasi === self::STATUS_MENUNGGU_REVIEW;
    }

    /** Cek apakah guru mengubah keputusan dari rekomendasi sistem */
    public function isKategoriDiubahGuru(): bool
    {
        return $this->kategori_keputusan_guru
            && $this->kategori_rekomendasi_sistem
            && $this->kategori_keputusan_guru !== $this->kategori_rekomendasi_sistem;
    }

    public function getStatusValidasiBadgeAttribute(): string
    {
        return match ($this->status_validasi) {
            self::STATUS_DISETUJUI_GURU  => '<span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-green-100 text-green-700 border border-green-200">Disetujui Guru</span>',
            default                       => '<span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-700 border border-amber-200">Menunggu Review</span>',
        };
    }

    // 🔥 Tentukan kategori akhir otomatis
    public function tentukanKategori(): string
    {
        $kategori = KategoriNilai::findByNilai($this->nilai_akhir);
        return $kategori?->nama ?? '-';
    }

    // 🔥 Ambil nilai per subkriteria
    public function nilaiPerSubkriteria()
    {
        return $this->detail()->with('subkriteria.kriteria')->get();
    }
}
