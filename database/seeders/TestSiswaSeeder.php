<?php
 
namespace Database\Seeders;
 
use App\Models\Siswa;
use Illuminate\Database\Seeder;
 
class TestSiswaSeeder extends Seeder
{
    public function run(): void
    {
        Siswa::create([
            'name' => 'Test Siswa',
            'kelas_id' => 'K001',
            'tanggal_lahir' => '2010-01-01',
            'jenis_kelamin' => 'L',
        ]);
    }
}
