<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateRekomendasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'kategori' => 'MB',
                'isi' => '{{nama_siswa}} masih memerlukan bimbingan lebih lanjut dalam mengembangkan potensinya. Nilai akhir: {{nilai}} ({{kategori}}). Terus berikan motivasi dan dukungan di rumah.',
                'prioritas' => 'tinggi',
            ],
            [
                'kategori' => 'BSH',
                'isi' => '{{nama_siswa}} menunjukkan perkembangan yang baik dan sesuai harapan. Nilai akhir: {{nilai}} ({{kategori}}). Pertahankan dan tingkatkan prestasi belajarnya.',
                'prioritas' => 'menengah',
            ],
            [
                'kategori' => 'BSB',
                'isi' => '{{nama_siswa}} menunjukkan prestasi yang sangat baik dan melampaui harapan. Nilai akhir: {{nilai}} ({{kategori}}). Terus kembangkan bakat dan potensinya agar semakin optimal.',
                'prioritas' => 'rendah',
            ],
        ];

        foreach ($templates as $template) {
            DB::table('template_rekomendasi')->updateOrInsert(
                ['kategori' => $template['kategori'], 'subkriteria_id' => null],
                $template
            );
        }
    }
}
