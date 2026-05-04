<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateRekomendasiUmum;
use Illuminate\Http\Request;

class TemplateRekomendasiUmumController extends Controller
{
    public function index()
    {
        $templates = TemplateRekomendasiUmum::all();
        $categoriesWithUtama = TemplateRekomendasiUmum::where('prioritas', 'utama')->pluck('kategori')->toArray();
        return view('admin.template_rekomendasi_umum', compact('templates', 'categoriesWithUtama'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori'  => 'required|string|max:5',
            'isi'       => 'required|string',
            'prioritas' => 'required|in:utama,alternatif',
        ]);

        if ($request->prioritas === 'utama') {
            $exists = TemplateRekomendasiUmum::where('kategori', $request->kategori)
                ->where('prioritas', 'utama')
                ->exists();

            if ($exists) {
                return back()->with('error', "Kategori {$request->kategori} sudah memiliki template utama. Ubah template utama yang ada terlebih dahulu.")->withInput();
            }
        }

        TemplateRekomendasiUmum::create($request->all());

        return back()->with('success', 'Template umum berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori'  => 'required|string|max:5',
            'isi'       => 'required|string',
            'prioritas' => 'required|in:utama,alternatif',
        ]);

        $template = TemplateRekomendasiUmum::findOrFail($id);

        if ($request->prioritas === 'utama' && $template->prioritas !== 'utama') {
            $exists = TemplateRekomendasiUmum::where('kategori', $request->kategori)
                ->where('prioritas', 'utama')
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()->with('error', "Kategori {$request->kategori} sudah memiliki template utama.")->withInput();
            }
        }

        $template->update($request->all());

        return back()->with('success', 'Template umum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $template = TemplateRekomendasiUmum::findOrFail($id);
        $template->delete();

        return back()->with('success', 'Template umum berhasil dihapus.');
    }
}
