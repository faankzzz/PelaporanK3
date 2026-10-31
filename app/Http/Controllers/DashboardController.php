<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $base = Incident::query();
        if ($user->isPelapor()) {
            $base->where('reporter_id', $user->id);
        }

        $total = (clone $base)->count();
        $byStatus = (clone $base)->selectRaw('status, count(*) as jml')->groupBy('status')->pluck('jml', 'status');
        $bySeverity = (clone $base)->whereNotNull('severity')->selectRaw('severity, count(*) as jml')->groupBy('severity')->pluck('jml', 'severity');

        $byType = (clone $base)->with('type')->get()->groupBy(fn ($i) => $i->type->nama)->map->count()->sortDesc();

        // 6 bulan terakhir
        $bulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan[now()->subMonths($i)->format('Y-m')] = 0;
        }
        (clone $base)->where('tanggal_kejadian', '>=', now()->subMonths(5)->startOfMonth())->get()
            ->each(function ($i) use (&$bulan) {
                $k = $i->tanggal_kejadian->format('Y-m');
                if (isset($bulan[$k])) $bulan[$k]++;
            });

        // Hari tanpa kecelakaan kerja (berdasarkan jenis "Kecelakaan Kerja")
        $tipeKecelakaan = IncidentType::where('nama', 'Kecelakaan Kerja')->first();
        $hariTanpaKecelakaan = null;
        if ($tipeKecelakaan) {
            $terakhir = Incident::where('incident_type_id', $tipeKecelakaan->id)->max('tanggal_kejadian');
            $hariTanpaKecelakaan = $terakhir ? (int) \Carbon\Carbon::parse($terakhir)->startOfDay()->diffInDays(now()->startOfDay()) : null;
        }

        $terbaru = (clone $base)->with(['type', 'unit'])->latest()->take(5)->get();

        return view('dashboard', compact('total', 'byStatus', 'bySeverity', 'byType', 'bulan', 'hariTanpaKecelakaan', 'terbaru'));
    }
}
