<?php

namespace App\Http\Controllers;

use App\Models\CorrectiveAction;
use App\Models\Incident;
use App\Support\Audit;
use Illuminate\Http\Request;

class K3Controller extends Controller
{
    public function verify(Request $request, Incident $incident)
    {
        abort_unless($incident->status === 'dilaporkan', 422, 'Laporan sudah diverifikasi.');

        $data = $request->validate([
            'severity'           => 'required|in:ringan,sedang,berat,fatal',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $incident->update($data + [
            'status'      => 'diverifikasi',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        Audit::log('verifikasi', "Memverifikasi {$incident->kode} (keparahan: {$data['severity']})");

        return back()->with('success', 'Laporan diverifikasi.');
    }

    public function investigation(Request $request, Incident $incident)
    {
        abort_unless(in_array($incident->status, ['diverifikasi', 'investigasi', 'tindak_lanjut']), 422, 'Laporan belum dapat diinvestigasi.');

        $data = $request->validate([
            'severity'          => 'required|in:ringan,sedang,berat,fatal',
            'penyebab_langsung' => 'required|string',
            'penyebab_dasar'    => 'required|string',
            'hasil_investigasi' => 'required|string',
        ]);

        if ($incident->status === 'diverifikasi') {
            $data['status'] = 'investigasi';
        }
        $incident->update($data);

        Audit::log('investigasi', "Mengisi hasil investigasi {$incident->kode}");

        return back()->with('success', 'Hasil investigasi disimpan.');
    }

    public function storeAction(Request $request, Incident $incident)
    {
        abort_unless(in_array($incident->status, ['investigasi', 'tindak_lanjut']), 422, 'Tindakan hanya dapat ditambah saat investigasi/tindak lanjut.');

        $data = $request->validate([
            'deskripsi'        => 'required|string',
            'penanggung_jawab' => 'required|string|max:255',
            'tenggat'          => 'required|date|after_or_equal:today',
        ]);

        $incident->actions()->create($data);
        Audit::log('tambah_tindakan', "Menambah tindakan korektif pada {$incident->kode}");

        return back()->with('success', 'Tindakan korektif/preventif ditambahkan.');
    }

    public function updateAction(Request $request, CorrectiveAction $action)
    {
        $data = $request->validate(['status' => 'required|in:belum,proses,selesai']);
        $action->update($data);

        Audit::log('ubah_tindakan', "Status tindakan #{$action->id} pada {$action->incident->kode} menjadi {$data['status']}");

        return back()->with('success', 'Status tindakan diperbarui.');
    }

    public function status(Request $request, Incident $incident)
    {
        $request->validate(['status' => 'required|in:tindak_lanjut,selesai']);
        $target = $request->status;

        if ($target === 'tindak_lanjut') {
            abort_unless($incident->status === 'investigasi', 422, 'Status hanya dapat dimajukan dari Investigasi.');
            abort_if(! $incident->penyebab_langsung, 422, 'Isi hasil investigasi terlebih dahulu.');
            $incident->update(['status' => 'tindak_lanjut']);
        } else {
            abort_unless($incident->status === 'tindak_lanjut', 422, 'Laporan harus berstatus Tindak Lanjut sebelum ditutup.');
            if ($incident->actions()->count() === 0 || $incident->actions()->where('status', '!=', 'selesai')->exists()) {
                return back()->with('error', 'Laporan belum dapat ditutup: harus ada tindakan korektif dan semuanya berstatus selesai.');
            }
            $incident->update(['status' => 'selesai', 'closed_at' => now()]);
        }

        Audit::log('ubah_status', "Status {$incident->kode} menjadi {$target}");

        return back()->with('success', 'Status laporan diperbarui.');
    }
}
