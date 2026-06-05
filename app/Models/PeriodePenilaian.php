<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;

class PeriodePenilaian extends Model
{
    use HasCustomId;
    public $timestamps = true;
    protected $table = 'periode_penilaian';
    protected $primaryKey = 'id_periode';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'P';
    }

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_periode',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_aktif',
        'status',
        'finalized_at',
        'created_by',
    ];

    const STATUS_DRAFT  = 'draft';
    const STATUS_AKTIF  = 'aktif';
    const STATUS_PROSES = 'proses';
    const STATUS_FINAL  = 'final';

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'is_aktif'        => 'boolean',
        'finalized_at'    => 'datetime',
    ];

    /**
     * Booted logic
     */
    protected static function booted()
    {
        static::saving(function ($periode) {
            // Jika satu periode diaktifkan, maka semua periode lain otomatis nonaktif
            if ($periode->is_aktif) {
                // Nonaktifkan periode lain yang saat ini aktif
                static::where('id_periode', '!=', $periode->id_periode)
                    ->where('is_aktif', true)
                    ->update([
                        'is_aktif' => false,
                        'status' => self::STATUS_DRAFT
                    ]);
            }

            // Sync is_aktif with status if necessary
            if ($periode->status === self::STATUS_AKTIF) {
                $periode->is_aktif = true;
            } elseif (in_array($periode->status, [self::STATUS_DRAFT, self::STATUS_PROSES, self::STATUS_FINAL])) {
                $periode->is_aktif = false;
            }
        });
    }

    // ================= RELASI =================

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'periode_kelas', 'periode_id', 'kelas_id')
                    ->using(PeriodeKelas::class)
                    ->withTimestamps();
    }

    public function minggu()
    {
        return $this->hasMany(MingguPenilaian::class, 'periode_id');
    }

    public function evaluasi()
    {
        return $this->hasMany(Evaluasi::class, 'periode_id');
    }

    /**
     * Helper to check if this period has any weekly scores
     */
    public function hasPenilaian(): bool
    {
        return $this->minggu()->whereHas('jadwalSubkriteria.penilaian')->exists();
    }

    // ================= HELPER =================

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_FINAL  => '<span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-indigo-100 text-indigo-700 border border-indigo-200">Final</span>',
            self::STATUS_PROSES => '<span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-700 border border-amber-200">Proses</span>',
            self::STATUS_AKTIF  => '<span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-green-100 text-green-700 border border-green-200">Aktif</span>',
            default             => '<span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-slate-100 text-slate-500 border border-slate-200">Draft</span>',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->where('status', self::STATUS_AKTIF);
    }

    public function isFinal(): bool
    {
        return $this->status === self::STATUS_FINAL;
    }

    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    public function isProses(): bool
    {
        return $this->status === self::STATUS_PROSES;
    }

    /** Cek apakah semua evaluasi pada periode ini sudah divalidasi guru */
    public function allEvaluasiValidated(): bool
    {
        $totalEval = $this->evaluasi()->count();
        if ($totalEval === 0) return false;
        $validated = $this->evaluasi()->where('status_validasi', 'disetujui_guru')->count();
        return $totalEval === $validated;
    }

    /** Progress validasi guru */
    public function getValidasiProgress(): array
    {
        $total = $this->evaluasi()->count();
        $done  = $this->evaluasi()->where('status_validasi', 'disetujui_guru')->count();
        return ['total' => $total, 'done' => $done, 'pending' => $total - $done];
    }

    /** Cek apakah periode sudah bisa dipublikasikan (semua guru sudah validasi) */
    public function canBePublished(): bool
    {
        return $this->status === self::STATUS_PROSES && $this->allEvaluasiValidated();
    }

    public function canBeFinalized(): bool
    {
        if ($this->status !== self::STATUS_AKTIF) return false;
        
        // Cek apakah semua minggu sudah 'selesai'
        $totalMinggu = $this->minggu()->count();
        if ($totalMinggu === 0) return false;

        $jumlahSelesai = $this->minggu()->where('status', 'selesai')->count();
        return $totalMinggu === $jumlahSelesai;
    }
}
