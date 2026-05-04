<?php

namespace Database\Seeders;

use App\Models\TemplateRekomendasiUmum;
use Illuminate\Database\Seeder;

class TemplateRekomendasiUmumSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // 🟥 MB
            [
                'kategori'  => 'MB',
                'isi'       => 'Anak sudah mulai menunjukkan perkembangan, namun masih memerlukan pendampingan lebih lanjut, terutama pada aspek {{aspek}} agar dapat berkembang secara optimal.',
                'prioritas' => 'utama',
            ],
            [
                'kategori'  => 'MB',
                'isi'       => 'Dalam periode ini, anak mulai memperlihatkan minat belajar. Dukungan intensif masih sangat dibutuhkan pada {{aspek}} guna menunjang kemajuan perkembangannya.',
                'prioritas' => 'alternatif',
            ],
            
            // 🟨 BSH
            [
                'kategori'  => 'BSH',
                'isi'       => 'Secara umum anak sudah berkembang dengan baik sesuai harapan. Namun, masih perlu peningkatan pada aspek {{aspek}} agar hasil perkembangan menjadi lebih maksimal.',
                'prioritas' => 'utama',
            ],
            [
                'kategori'  => 'BSH',
                'isi'       => 'Perkembangan anak menunjukkan stabilitas yang baik sesuai usianya. Stimulasi berkelanjutan pada {{aspek}} akan membantu anak mencapai kompetensi yang lebih tinggi.',
                'prioritas' => 'alternatif',
            ],

            // 🟩 BSB
            [
                'kategori'  => 'BSB',
                'isi'       => 'Anak menunjukkan perkembangan yang sangat baik dan konsisten di berbagai aspek. Tetap berikan stimulasi pada {{aspek}} agar kemampuan anak dapat terus berkembang secara optimal.',
                'prioritas' => 'utama',
            ],
            [
                'kategori'  => 'BSB',
                'isi'       => 'Capaian perkembangan anak sangat membanggakan di hampir seluruh kriteria. Pendampingan pada {{aspek}} tetap disarankan untuk menjaga konsistensi kemampuannya.',
                'prioritas' => 'alternatif',
            ],
        ];

        // Kosongkan dulu agar bersih
        TemplateRekomendasiUmum::truncate();

        foreach ($data as $item) {
            TemplateRekomendasiUmum::create($item);
        }
    }
}
