<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Unit;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        return view('admin.master', [
            'units' => Unit::orderBy('nama')->get(),
            'types' => IncidentType::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, string $tabel)
    {
        $model = $this->model($tabel);
        $table = $model->getTable();

        $data = $request->validate(['nama' => "required|string|max:255|unique:$table,nama"]);
        $model->create($data);

        Audit::log('tambah_master', "Menambah $table: {$data['nama']}");

        return back()->with('success', 'Data ditambahkan.');
    }

    public function destroy(string $tabel, int $id)
    {
        $model = $this->model($tabel);
        $row = $model->findOrFail($id);

        $dipakai = $tabel === 'unit'
            ? Incident::where('unit_id', $id)->exists() || User::where('unit_id', $id)->exists()
            : Incident::where('incident_type_id', $id)->exists();

        if ($dipakai) {
            return back()->with('error', 'Data tidak dapat dihapus karena sedang dipakai.');
        }

        Audit::log('hapus_master', "Menghapus {$model->getTable()}: {$row->nama}");
        $row->delete();

        return back()->with('success', 'Data dihapus.');
    }

    private function model(string $tabel)
    {
        return match ($tabel) {
            'unit'  => new Unit(),
            'jenis' => new IncidentType(),
            default => abort(404),
        };
    }
}
