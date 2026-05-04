<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

$phpWord = new PhpWord();

// Define styles
$phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16, 'color' => '333333'], ['alignment' => 'center']);
$phpWord->addFontStyle('HeaderStyle', ['bold' => true, 'size' => 11]);
$phpWord->addFontStyle('BodyStyle', ['size' => 11]);

$section = $phpWord->addSection();

// KOP SURAT
$header = $section->addHeader();
$header->addText('TK PEMBINA KOTA', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
$header->addText('Jl. Pendidikan No. 45, Pusat Kota | Telp: (021) 555-0123', ['italic' => true, 'size' => 9], ['alignment' => 'center']);
$header->addTextBreak(1);

// TITLE
$section->addTitle('LAPORAN PERKEMBANGAN SISWA', 1);
$section->addTextBreak(1);

// SISWA INFO
$table = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
$table->addRow();
$table->addCell(2000)->addText('Nama Siswa', 'HeaderStyle');
$table->addCell(500)->addText(':', 'HeaderStyle');
$table->addCell(5000)->addText('${NAMA_SISWA}', 'BodyStyle');

$table->addRow();
$table->addCell(2000)->addText('NISN', 'HeaderStyle');
$table->addCell(500)->addText(':', 'HeaderStyle');
$table->addCell(5000)->addText('${NISN}', 'BodyStyle');

$table->addRow();
$table->addCell(2000)->addText('Kelas', 'HeaderStyle');
$table->addCell(500)->addText(':', 'HeaderStyle');
$table->addCell(5000)->addText('${KELAS}', 'BodyStyle');

$table->addRow();
$table->addCell(2000)->addText('Tanggal Cetak', 'HeaderStyle');
$table->addCell(500)->addText(':', 'HeaderStyle');
$table->addCell(5000)->addText('${TANGGAL}', 'BodyStyle');

$section->addTextBreak(1);

// RINGKASAN
$section->addText('RINGKASAN CAPAIAN AKHIR', ['bold' => true, 'size' => 12]);
$tableFinal = $section->addTable(['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80]);
$tableFinal->addRow();
$tableFinal->addCell(4000)->addText('Skor Akhir (SPK Fuzzy SMART)', 'HeaderStyle');
$tableFinal->addCell(4000)->addText('Kategori Perkembangan', 'HeaderStyle');
$tableFinal->addRow();
$tableFinal->addCell(4000)->addText('${NILAI_AKHIR}%', ['bold' => true, 'size' => 14, 'color' => '0000FF']);
$tableFinal->addCell(4000)->addText('${KATEGORI_AKHIR}', ['bold' => true, 'size' => 14, 'color' => '008000']);

$section->addTextBreak(1);

// REKOMENDASI
$section->addText('REKOMENDASI PENGEMBANGAN:', 'HeaderStyle');
$section->addText('${REKOMENDASI}', ['italic' => true, 'size' => 11]);
$section->addTextBreak(1);

// TABEL KRITERIA
$section->addText('I. CAPAIAN PER ASPEK PERKEMBANGAN', ['bold' => true, 'size' => 12]);
$tableKrit = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 50]);
$tableKrit->addRow();
$tableKrit->addCell(1000)->addText('Kode', 'HeaderStyle');
$tableKrit->addCell(4500)->addText('Aspek Perkembangan', 'HeaderStyle');
$tableKrit->addCell(1500)->addText('Skor', 'HeaderStyle');
$tableKrit->addCell(1500)->addText('Kategori', 'HeaderStyle');

// Row for cloning
$tableKrit->addRow();
$tableKrit->addCell(1000)->addText('${KRIT_KODE}', 'BodyStyle');
$tableKrit->addCell(4500)->addText('${KRIT_NAMA}', 'BodyStyle');
$tableKrit->addCell(1500)->addText('${KRIT_SKOR}', 'BodyStyle');
$tableKrit->addCell(1500)->addText('${KRIT_KAT}', 'BodyStyle');

$section->addTextBreak(1);

// TABEL SUBKRITERIA
$section->addText('II. DETAIL INDIKATOR PERKEMBANGAN', ['bold' => true, 'size' => 12]);
$tableSub = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 50]);
$tableSub->addRow();
$tableSub->addCell(1000)->addText('Kode', 'HeaderStyle');
$tableSub->addCell(3500)->addText('Indikator / Subkriteria', 'HeaderStyle');
$tableSub->addCell(1500)->addText('Capaian', 'HeaderStyle');
$tableSub->addCell(2500)->addText('Catatan Observasi', 'HeaderStyle');

// Row for cloning
$tableSub->addRow();
$tableSub->addCell(1000)->addText('${SUB_KODE}', 'BodyStyle');
$tableSub->addCell(3500)->addText('${SUB_NAMA}', 'BodyStyle');
$tableSub->addCell(1500)->addText('${SUB_KAT}', 'BodyStyle');
$tableSub->addCell(2500)->addText('${SUB_CAT}', 'BodyStyle');

$section->addTextBreak(1);

// CATATAN GURU
$section->addText('III. KESIMPULAN GURU WALI KELAS', ['bold' => true, 'size' => 12]);
$section->addText('${CATATAN_GURU}', 'BodyStyle');

$section->addTextBreak(2);

// TANDA TANGAN
$tableSign = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
$tableSign->addRow(1000);
$tableSign->addCell(4500)->addText('Orang Tua / Wali,', 'BodyStyle', ['alignment' => 'center']);
$tableSign->addCell(4500)->addText('Guru Wali Kelas,', 'BodyStyle', ['alignment' => 'center']);
$tableSign->addRow(1500);
$tableSign->addCell(4500)->addText('', 'BodyStyle');
$tableSign->addCell(4500)->addText('', 'BodyStyle');
$tableSign->addRow();
$tableSign->addCell(4500)->addText('(....................................)', 'BodyStyle', ['alignment' => 'center']);
$tableSign->addCell(4500)->addText('( ${GURU_NAME} )', 'BodyStyle', ['alignment' => 'center']);

// Save file
$directory = 'storage/app/public/templates';
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

$fileName = $directory . '/template_laporan.docx';
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($fileName);

echo "Template created at: " . $fileName;
