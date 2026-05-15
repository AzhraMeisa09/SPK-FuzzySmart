<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateRekomendasi;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateRekomendasiController extends Controller
{
    /**
     * Step 1: Tampilkan daftar Kriteria
     */
    public function index()
    {
        $kriterias = Kriteria::where('is_aktif', true)->withCount('subkriteria')->get();
        return view('admin.template_rekomendasi.index', compact('kriterias'));
    }

    /**
     * Step 2: Tampilkan subkriteria dari kriteria terpilih
     */
    public function showSubkriteria($kriteria_id)
    {
        $kriteria = Kriteria::with(['subkriteria' => function($q) {
            $q->orderByRaw("LENGTH(id_subkriteria) ASC, id_subkriteria ASC");
        }])->findOrFail($kriteria_id);
        
        return view('admin.template_rekomendasi.step2', compact('kriteria'));
    }

    /**
     * Step 2 → Step 3: Auto-generate & simpan ke DB, redirect ke hasil
     */
    public function generate(Request $request)
    {
        $request->validate([
            'subkriteria_ids'   => 'required|array|min:1',
            'subkriteria_ids.*' => 'exists:subkriteria,id_subkriteria',
        ]);

        $subkriteria_ids = $request->subkriteria_ids;
        $subkriterias    = Subkriteria::whereIn('id_subkriteria', $subkriteria_ids)->get();

        $pola = [
            'MB'  => ['isi' => fn($nama) => "Perlu bimbingan dalam {$nama}",  'prioritas' => 'tinggi'],
            'BSH' => ['isi' => fn($nama) => "Cukup baik dalam {$nama}",       'prioritas' => 'sedang'],
            'BSB' => ['isi' => fn($nama) => "Sangat baik dalam {$nama}",      'prioritas' => 'rendah'],
        ];

        $inserted = 0;
        foreach ($subkriterias as $s) {
            foreach ($pola as $kategori => $cfg) {
                $created = TemplateRekomendasi::firstOrCreate(
                    ['subkriteria_id' => $s->id_subkriteria, 'kategori' => $kategori],
                    ['isi' => $cfg['isi']($s->nama_subkriteria), 'prioritas' => $cfg['prioritas']]
                );
                if ($created->wasRecentlyCreated) {
                    $inserted++;
                }
            }
        }

        session(['tr_subkriteria_ids' => $subkriteria_ids]);

        $msg = $inserted > 0
            ? "{$inserted} template baru berhasil digenerate."
            : 'Template sudah ada, tidak ada yang ditambahkan.';

        return redirect()->route('admin.template-rekomendasi.generated')
            ->with('success', $msg);
    }

    /**
     * Step 3: Tampilkan template dari DB (edit & delete)
     */
    public function showGenerated()
    {
        $subkriteria_ids = session('tr_subkriteria_ids', []);

        if (empty($subkriteria_ids)) {
            return redirect()->route('admin.template-rekomendasi.index')
                ->with('error', 'Sesi habis. Silakan ulangi prosesnya.');
        }

        $templates = TemplateRekomendasi::with('subkriteria.kriteria')
            ->whereIn('subkriteria_id', $subkriteria_ids)
            ->orderBy('subkriteria_id')
            ->orderByRaw("FIELD(kategori, 'MB', 'BSH', 'BSB')")
            ->get();

        return view('admin.template_rekomendasi.generated', compact('templates'));
    }

    /**
     * Update satu template (inline edit)
     */
    public function update(Request $request, TemplateRekomendasi $template)
    {
        $request->validate([
            'isi'       => 'required|string',
            'prioritas' => 'required|in:tinggi,sedang,rendah',
        ]);

        $template->update($request->only('isi', 'prioritas'));

        return back()->with('success', 'Template berhasil diperbarui.');
    }

    /**
     * Hapus satu template
     */
    public function destroy(TemplateRekomendasi $template)
    {
        $template->delete();
        return back()->with('success', 'Template berhasil dihapus.');
    }
}
