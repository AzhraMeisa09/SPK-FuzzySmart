<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasCustomId;

class MingguPenilaian extends Model
{
    use HasCustomId;
    public $timestamps = true;

    protected $table = 'minggu_penilaian';
    protected $primaryKey = 'id_minggu';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getPrefix()
    {
        return 'M';
    }

    protected $fillable = [
        'periode_id',
        'minggu_ke',
        'tema',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];

    protected $casts = [
        'minggu_ke'       => 'integer',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ================= RELASI =================

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function jadwalSubkriteria()
    {
        return $this->hasMany(JadwalSubkriteria::class, 'minggu_id');
    }

    public function subkriteria()
    {
        return $this->belongsToMany(Subkriteria::class, 'jadwal_subkriteria', 'minggu_id', 'subkriteria_id')
                    ->using(JadwalSubkriteria::class)
                    ->withPivot('urutan', 'wajib')
                    ->withTimestamps();
    }

    public function portofolio()
    {
        return $this->hasMany(Portofolio::class, 'minggu_id');
    }

    // ================= HELPER =================

    public function isSelesai()
    {
        return $this->status === 'selesai';
    }

    public function isAktif()
    {
        return $this->status === 'aktif';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function mingguSebelumnya()
    {
        return self::where('periode_id', $this->periode_id)
            ->where('minggu_ke', '<', $this->minggu_ke)
            ->orderBy('minggu_ke', 'desc')
            ->first();
    }

    /**
     * Logic: Minggu ke-N hanya bisa aktif jika minggu ke-(N-1) sudah selesai.
     */
    public function bolehDiisi()
    {
        $prev = $this->mingguSebelumnya();
        if (!$prev) return true;
        return $prev->isSelesai();
    }

    /**
     * Logic: Cek apakah semua subkriteria sudah memiliki penilaian untuk semua siswa di semua kelas dalam periode ini.
     */
    public function sudahDinilai()
    {
        // Ambil semua ID siswa yang terlibat dalam periode ini (melalui kelas)
        $siswaIds = $this->periode->kelas->flatMap->siswa->pluck('id_siswa')->unique();
        $totalSiswa = $siswaIds->count();
        
        if ($totalSiswa === 0) return false;

        $jadwalIds = $this->jadwalSubkriteria->pluck('id_jadwal_sub');
        if ($jadwalIds->isEmpty()) return false;

        // Hitung berapa banyak baris penilaian (jadwal_sub_id, siswa_id) yang SUDAH FINAL
        $totalSudahFinal = PenilaianMingguan::whereIn('jadwal_sub_id', $jadwalIds)
            ->whereIn('siswa_id', $siswaIds)
            ->where('status', 'final')
            ->count();

        // Total yang HARUSNYA ada = Jumlah Subkriteria * Jumlah Siswa
        $totalHarusDinilai = $jadwalIds->count() * $totalSiswa;

        // Harus lengkap dan statusnya FINAL semua
        return $totalSudahFinal >= $totalHarusDinilai;
    }
}
