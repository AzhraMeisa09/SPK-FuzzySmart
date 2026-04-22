<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Kriteria::withCount('subkriteria');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama', 'like', "%{$search}%");
        }

        $kriterias = $query->orderBy('id')->get();
        $totalBobot = Kriteria::sum('bobot'); // Tetap hitung total bobot asli untuk validasi UI
        
        return view('admin.kriteria', compact('kriterias', 'totalBobot'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'bobot' => 'required|numeric|min:0|max:1'
        ]);

        // Check total weight
        $currentTotal = Kriteria::sum('bobot');
        if (($currentTotal + $request->bobot) > 1.0001) { // Floating point buffer
            return redirect()->back()->with('error', 'Gagal! Total bobot melebihi 100% (saat ini: ' . ($currentTotal * 100) . '%)')->withInput();
        }

        Kriteria::create([
            'nama' => $request->nama,
            'bobot' => $request->bobot
        ]);

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil ditambahkan');
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
                'bobot' => 'required|numeric|min:0|max:1'
            ]);

            // Check total weight (excluding current kriteria weight)
            $currentTotal = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
            if (($currentTotal + $request->bobot) > 1.0001) {
                return redirect()->back()
                    ->with('error', 'Gagal! Total bobot melebihi 100% (saat ini: ' . ($currentTotal * 100) . '%)')
                    ->with('edit_id', $kriteria->id)
                    ->with('edit_data', $request->all())
                    ->withInput();
            }

            $kriteria->update($request->only('nama', 'bobot'));

            return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('edit_id', $kriteria->id)
                ->with('edit_data', $request->all())
                ->withInput();
        }
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil dihapus');
    }
}
