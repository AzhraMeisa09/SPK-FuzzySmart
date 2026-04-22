# Dokumentasi Relasi Many-to-Many: Periode & Kelas

Setelah melakukan migrasi, relasi antara `PeriodePenilaian` dan `Kelas` sekarang bersifat **Many-to-Many**. Satu periode (misal: Semester 1 2023/2024) bisa digunakan oleh banyak kelas sekaligus.

## 1. Menyimpan Data (Insert)

Saat membuat periode baru dan memilih beberapa kelas:

```php
use App\Models\PeriodePenilaian;
use App\Models\Kelas;

// 1. Buat Header Periode
$periode = PeriodePenilaian::create([
    'tahun_ajaran_id' => $tahunAjaranId,
    'semester'        => '1',
    'status'          => 'aktif',
    'created_by'      => auth()->id(),
]);

// 2. Hubungkan ke banyak Kelas (Misal dari request select multiple)
$kelasIds = [1, 2, 3]; // Contoh ID kelas dari UI checkbox/select2
$periode->kelas()->attach($kelasIds);

// Atau jika ingin sinkronisasi (hapus yang lama, ganti yang baru)
// $periode->kelas()->sync($kelasIds);
```

## 2. Mengambil Data (Query)

### Mengambil Kelas dari sebuah Periode
```php
$periode = PeriodePenilaian::find($id);
$daftarKelas = $periode->kelas; // Mengembalikan Collection of Kelas

foreach ($daftarKelas as $kelas) {
    echo $kelas->nama_kelas;
}
```

### Mengambil Periode dari sebuah Kelas
```php
$kelas = Kelas::where('nama_kelas', 'X-IPA-1')->first();
$daftarPeriode = $kelas->periode; // Mengembalikan Collection of PeriodePenilaian
```

### Cek apakah Periode memiliki Kelas tertentu
```php
if ($periode->kelas()->where('kelas.id', $kelasId)->exists()) {
    // Kelas ini termasuk dalam periode ini
}
```

## 3. Optimasi Penilaian
Karena sekarang periode tidak memiliki `kelas_id`, pastikan query penilaian tetap akurat dengan memfilter kelas melalui relasi atau tabel pivot jika diperlukan. Namun, relasi `evaluasi` dan `minggu_penilaian` tetap langsung ke `periode_id`, sehingga logika perhitungan SPK Anda seharusnya tidak terganggu selama siswa difilter berdasarkan kelasnya masing-masing.
