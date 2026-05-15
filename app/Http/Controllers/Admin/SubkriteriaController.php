<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;

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
            $selectedKriteria = Kriteria::orderBy('id_kriteria')->first();
        }

        $query = $selectedKriteria ? $selectedKriteria->subkriteria() : Subkriteria::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama_subkriteria', 'like', "%{$search}%");
        }

        $subkriterias = $query->orderByRaw("LENGTH(id_subkriteria) ASC, id_subkriteria ASC")->get();
        $allKriteria = Kriteria::orderByRaw("LENGTH(id_kriteria) ASC, id_kriteria ASC")->get();

        return view('admin.subkriteria', compact('allKriteria', 'selectedKriteria', 'subkriterias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id_kriteria',
            'nama_subkriteria' => 'required|string|max:250',
            'rubrik_mb' => 'required|string',
            'rubrik_bsh' => 'required|string',
            'rubrik_bsb' => 'required|string',
        ]);

        $kriteria = Kriteria::findOrFail($request->kriteria_id);

        Subkriteria::create([
            'kriteria_id' => $request->kriteria_id,
            'nama_subkriteria' => $request->nama_subkriteria,
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
                'nama_subkriteria' => 'required|string|max:250',
                'rubrik_mb' => 'required|string',
                'rubrik_bsh' => 'required|string',
                'rubrik_bsb' => 'required|string',
            ]);

            $subkriteria->update([
                'nama_subkriteria' => $request->nama_subkriteria,
                'rubrik_mb' => $request->rubrik_mb,
                'rubrik_bsh' => $request->rubrik_bsh,
                'rubrik_bsb' => $request->rubrik_bsb,
            ]);

            return redirect()->route('admin.subkriteria.index', ['kriteria_id' => $subkriteria->kriteria_id])
                ->with('success', 'Subkriteria berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('edit_id', $subkriteria->id_subkriteria)
                ->with('edit_data', $request->all())
                ->withInput();
        }
    }

    public function importWord(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id_kriteria',
            'file' => 'required|mimes:docx|max:5120',
        ]);

        try {
            $path = $request->file('file')->getRealPath();
            $phpWord = IOFactory::load($path);
            $importedCount = 0;

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof Table) {
                        $rows = $element->getRows();
                        // Skip header row
                        for ($i = 1; $i < count($rows); $i++) {
                            $cells = $rows[$i]->getCells();
                            if (count($cells) >= 5) {
                                $nama = $this->extractText($cells[1]);
                                $mb = $this->extractText($cells[2]);
                                $bsh = $this->extractText($cells[3]);
                                $bsb = $this->extractText($cells[4]);

                                if ($nama) {
                                    Subkriteria::create([
                                        'kriteria_id' => $request->kriteria_id,
                                        'nama_subkriteria' => $nama,
                                        'rubrik_mb' => $mb ?: '-',
                                        'rubrik_bsh' => $bsh ?: '-',
                                        'rubrik_bsb' => $bsb ?: '-',
                                    ]);
                                    $importedCount++;
                                }
                            }
                        }
                    }
                }
            }

            return redirect()->back()->with('success', $importedCount . ' Subkriteria berhasil diimport dari Word.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    private function extractText($cell)
    {
        $text = '';
        foreach ($cell->getElements() as $element) {
            $text .= $this->processElement($element);
        }
        return trim($text);
    }

    private function processElement($element)
    {
        $text = '';
        if ($element instanceof Text) {
            $text .= $element->getText();
        } elseif ($element instanceof TextRun) {
            foreach ($element->getElements() as $childElement) {
                $text .= $this->processElement($childElement);
            }
        }
        return $text;
    }

    public function destroy(Subkriteria $subkriteria)
    {
        $kId = $subkriteria->kriteria_id;
        $subkriteria->delete();
        return redirect()->route('admin.subkriteria.index', ['kriteria_id' => $kId])
            ->with('success', 'Subkriteria berhasil dihapus');
    }
}
