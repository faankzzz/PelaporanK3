<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Unit;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    private function authorizeView(Incident $incident): void
    {
        $user = auth()->user();
        if ($user->isPelapor() && $incident->reporter_id !== $user->id) {
            abort(403, 'Anda hanya dapat melihat laporan milik sendiri.');
        }
    }

    private function rules(): array
    {
        return [
            'incident_type_id' => 'required|exists:incident_types,id',
            'unit_id'          => 'required|exists:units,id',
            'tanggal_kejadian' => 'nullable|date|before_or_equal:now',
            'lokasi_detail'    => 'required|string|max:255',
            'kronologi'        => 'required|string',
            'korban'           => 'nullable|string|max:255',
            'saksi'            => 'nullable|string|max:255',
            'foto'             => 'nullable|image|max:10240',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Incident::with(['type', 'unit', 'reporter'])->latest('tanggal_kejadian');

        if ($user->isPelapor()) {
            $query->where('reporter_id', $user->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('incident_type_id', $request->type);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) => $w->where('kode', 'like', "%$q%")
                ->orWhere('kronologi', 'like', "%$q%")
                ->orWhere('lokasi_detail', 'like', "%$q%"));
        }

        return view('incidents.index', [
            'incidents' => $query->paginate(10)->withQueryString(),
            'types'     => IncidentType::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('incidents.create', [
            'types' => IncidentType::orderBy('nama')->get(),
            'units' => Unit::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        if (empty($data['tanggal_kejadian'])) {
            $data['tanggal_kejadian'] = now();
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('insiden', 'public');
        }

        $data['reporter_id'] = auth()->id();
        $incident = Incident::create($data);
        $incident->update(['kode' => 'INS-' . now()->format('Ymd') . '-' . str_pad($incident->id, 4, '0', STR_PAD_LEFT)]);

        Audit::log('buat_laporan', "Membuat laporan {$incident->kode}");

        return redirect()->route('incidents.show', $incident)->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Incident $incident)
    {
        $this->authorizeView($incident);
        $incident->load(['type', 'unit', 'reporter', 'verifier', 'actions']);

        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $this->authorizeEdit($incident);

        return view('incidents.edit', [
            'incident' => $incident,
            'types'    => IncidentType::orderBy('nama')->get(),
            'units'    => Unit::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Incident $incident)
    {
        $this->authorizeEdit($incident);
        $data = $request->validate($this->rules());

        if ($request->hasFile('foto')) {
            if ($incident->foto) Storage::disk('public')->delete($incident->foto);
            $data['foto'] = $request->file('foto')->store('insiden', 'public');
        } else {
            unset($data['foto']);
        }

        $incident->update($data);
        Audit::log('ubah_laporan', "Mengubah laporan {$incident->kode}");

        return redirect()->route('incidents.show', $incident)->with('success', 'Laporan berhasil diperbarui.');
    }

    private function authorizeEdit(Incident $incident): void
    {
        if ($incident->reporter_id !== auth()->id() || $incident->status !== 'dilaporkan') {
            abort(403, 'Laporan hanya dapat diubah oleh pelapor sebelum diverifikasi.');
        }
    }

    public function print(Incident $incident)
    {
        $this->authorizeView($incident);
        $incident->load(['type', 'unit', 'reporter', 'verifier', 'actions']);

        return view('incidents.print', compact('incident'));
    }

    public function export()
    {
        $rows = Incident::with(['type', 'unit', 'reporter'])->orderBy('tanggal_kejadian')->get();
        $filename = 'laporan-insiden-k3-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar terbaca benar di Excel
            fputcsv($out, ['Kode', 'Tanggal Kejadian', 'Jenis', 'Unit/Area', 'Lokasi Detail', 'Pelapor', 'Korban', 'Saksi', 'Status', 'Tingkat Keparahan', 'Penyebab Langsung', 'Penyebab Dasar']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->kode, $r->tanggal_kejadian->format('d/m/Y H:i'), $r->type->nama, $r->unit->nama,
                    $r->lokasi_detail, $r->reporter->name, $r->korban, $r->saksi,
                    $r->statusLabel(), $r->severityLabel(), $r->penyebab_langsung, $r->penyebab_dasar,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
