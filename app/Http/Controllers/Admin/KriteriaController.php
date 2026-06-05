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
            $query->where('nama_kriteria', 'like', "%{$search}%");
        }

        $kriterias = $query->orderByRaw("LENGTH(id_kriteria) ASC, id_kriteria ASC")->get();
        $totalBobot = Kriteria::sum('bobot_kriteria'); // Tetap hitung total bobot asli untuk validasi UI
        
        return view('admin.kriteria', compact('kriterias', 'totalBobot'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:100|unique:kriteria,nama_kriteria',
            'bobot' => 'required|numeric|min:0|max:1'
        ]);

        // Check total weight
        $currentTotal = Kriteria::sum('bobot_kriteria');
        if (($currentTotal + $request->bobot) > 1.0001) { // Floating point buffer
            return redirect()->back()->with('error', 'Gagal! Total bobot melebihi 100% (saat ini: ' . ($currentTotal * 100) . '%)')->withInput();
        }

        Kriteria::create([
            'nama_kriteria' => $request->nama_kriteria,
            'bobot_kriteria' => $request->bobot,
            'is_aktif' => $request->has('is_aktif') ? $request->is_aktif : true
        ]);

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil ditambahkan');
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        try {
            $request->validate([
                'nama_kriteria' => 'required|string|max:100|unique:kriteria,nama_kriteria,' . $kriteria->id_kriteria . ',id_kriteria',
                'bobot' => 'required|numeric|min:0|max:1'
            ]);

            // Check total weight (excluding current kriteria weight)
            $currentTotal = Kriteria::where('id_kriteria', '!=', $kriteria->id_kriteria)->sum('bobot_kriteria');
            if (($currentTotal + $request->bobot) > 1.0001) {
                return redirect()->back()
                    ->with('error', 'Gagal! Total bobot melebihi 100% (saat ini: ' . ($currentTotal * 100) . '%)')
                    ->with('edit_id', $kriteria->id_kriteria)
                    ->with('edit_data', $request->all())
                    ->withInput();
            }

            $kriteria->update([
                'nama_kriteria' => $request->nama_kriteria,
                'bobot_kriteria' => $request->bobot,
                'is_aktif' => $request->has('is_aktif') ? $request->is_aktif : $kriteria->is_aktif
            ]);

            return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('edit_id', $kriteria->id_kriteria)
                ->with('edit_data', $request->all())
                ->withInput();
        }
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil dihapus');
    }

    public function toggleStatus(Kriteria $kriteria)
    {
        $kriteria->update([
            'is_aktif' => !$kriteria->is_aktif
        ]);

        $status = $kriteria->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.kriteria.index')->with('success', "Kriteria berhasil {$status}");
    }
}
