<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use Illuminate\Http\Request;

class SubkriteriaController extends Controller
{
    public function index(Request $request)
    {
        $kriteriaId = $request->get('kriteria_id');
        $selectedKriteria = null;

        if ($kriteriaId) {
            $selectedKriteria = Kriteria::with('subkriteria')->findOrFail($kriteriaId);
        } else {
            // Default to first kriteria if none selected
            $selectedKriteria = Kriteria::orderBy('id')->first();
        }

        $query = $selectedKriteria ? $selectedKriteria->subkriteria() : Subkriteria::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama', 'like', "%{$search}%");
        }

        $subkriterias = $query->get();
        $allKriteria = Kriteria::orderBy('id')->get();

        return view('admin.subkriteria', compact('allKriteria', 'selectedKriteria', 'subkriterias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'nama' => 'required|string|max:250',
            'rubrik_mb' => 'required|string',
            'rubrik_bsh' => 'required|string',
            'rubrik_bsb' => 'required|string',
        ]);

        $kriteria = Kriteria::findOrFail($request->kriteria_id);

        Subkriteria::create([
            'kriteria_id' => $request->kriteria_id,
            'nama' => $request->nama,
            'rubrik_mb' => $request->rubrik_mb,
            'rubrik_bsh' => $request->rubrik_bsh,
            'rubrik_bsb' => $request->rubrik_bsb,
        ]);

        return redirect()->route('admin.subkriteria.index', ['kriteria_id' => $request->kriteria_id])
            ->with('success', 'Subkriteria berhasil ditambahkan');
    }

    public function update(Request $request, Subkriteria $subkriteria)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:250',
                'rubrik_mb' => 'required|string',
                'rubrik_bsh' => 'required|string',
                'rubrik_bsb' => 'required|string',
            ]);

            $subkriteria->update($request->only('nama', 'rubrik_mb', 'rubrik_bsh', 'rubrik_bsb'));

            return redirect()->route('admin.subkriteria.index', ['kriteria_id' => $subkriteria->kriteria_id])
                ->with('success', 'Subkriteria berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('edit_id', $subkriteria->id)
                ->with('edit_data', $request->all())
                ->withInput();
        }
    }

    public function destroy(Subkriteria $subkriteria)
    {
        $kId = $subkriteria->kriteria_id;
        $subkriteria->delete();
        return redirect()->route('admin.subkriteria.index', ['kriteria_id' => $kId])
            ->with('success', 'Subkriteria berhasil dihapus');
    }
}
