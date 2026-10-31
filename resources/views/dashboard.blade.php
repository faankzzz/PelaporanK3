@extends('layouts.app')
@section('title', 'Dashboard K3')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-speedometer2 text-primary"></i> Dashboard Keselamatan Kerja (K3)
        </h4>
        <p class="text-muted small mb-0">Pemantauan insiden dan status tindak lanjut PT Pupuk Sriwidjaja Palembang.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('incidents.create') }}" class="btn btn-pusri-yellow d-flex align-items-center gap-2">
            <i class="bi bi-camera-fill"></i> Lapor Insiden Baru
        </a>
    </div>
</div>

{{-- Kartu Statistik Utama --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-pusri">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold">Total Laporan</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $total }}</div>
                </div>
                <div class="p-2 bg-light rounded-circle text-primary">
                    <i class="bi bi-folder-fill fs-4"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">Semua insiden tercatat</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card-pusri stat-yellow">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold">Dalam Proses</div>
                    <div class="fs-2 fw-bold text-warning mt-1">
                        {{ ($byStatus['dilaporkan'] ?? 0) + ($byStatus['diverifikasi'] ?? 0) + ($byStatus['investigasi'] ?? 0) + ($byStatus['tindak_lanjut'] ?? 0) }}
                    </div>
                </div>
                <div class="p-2 bg-warning-subtle rounded-circle text-warning">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">Verifikasi &amp; Investigasi</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card-pusri stat-green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold">Selesai / Closed</div>
                    <div class="fs-2 fw-bold text-success mt-1">{{ $byStatus['selesai'] ?? 0 }}</div>
                </div>
                <div class="p-2 bg-success-subtle rounded-circle text-success">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">Tindakan tuntas tervalidasi</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card-pusri stat-cyan">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold">Zero Accident Days</div>
                    <div class="fs-2 fw-bold text-primary mt-1">{{ $hariTanpaKecelakaan ?? '-' }}</div>
                </div>
                <div class="p-2 bg-primary-subtle rounded-circle text-primary">
                    <i class="bi bi-trophy-fill fs-4"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">Hari tanpa kecelakaan kerja</div>
        </div>
    </div>
</div>

{{-- Breakdown Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-pusri h-100">
            <div class="card-header header-accent">
                <span><i class="bi bi-diagram-3 me-2 text-primary"></i> Berdasarkan Status</span>
            </div>
            <ul class="list-group list-group-flush">
                @foreach(\App\Models\Incident::STATUS as $k => [$label, $color])
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="badge bg-{{ $color }} px-2 py-1">{{ $label }}</span>
                        <strong class="text-dark">{{ $byStatus[$k] ?? 0 }}</strong>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-pusri h-100">
            <div class="card-header header-accent">
                <span><i class="bi bi-shield-shaded me-2 text-danger"></i> Tingkat Keparahan</span>
            </div>
            <ul class="list-group list-group-flush">
                @foreach(\App\Models\Incident::SEVERITY as $k => [$label, $color])
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="badge bg-{{ $color }} px-2 py-1">{{ $label }}</span>
                        <strong class="text-dark">{{ $bySeverity[$k] ?? 0 }}</strong>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-pusri h-100">
            <div class="card-header header-accent">
                <span><i class="bi bi-tags me-2 text-warning"></i> Jenis Insiden</span>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($byType as $nama => $jml)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-dark small">{{ $nama }}</span>
                        <strong class="badge bg-secondary-subtle text-dark">{{ $jml }}</strong>
                    </li>
                @empty
                    <li class="list-group-item text-muted py-3 text-center">Belum ada data insiden.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- Tren 6 Bulan Terakhir --}}
<div class="card card-pusri mb-4">
    <div class="card-header header-accent">
        <span><i class="bi bi-graph-up-arrow me-2 text-primary"></i> Tren Insiden (6 Bulan Terakhir)</span>
    </div>
    <div class="card-body">
        @php $maks = max(1, max($bulan)); @endphp
        @foreach($bulan as $bln => $jml)
            <div class="d-flex align-items-center mb-2">
                <div style="width: 100px;" class="small fw-semibold text-secondary">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bln)->translatedFormat('M Y') }}
                </div>
                <div class="progress flex-grow-1" style="height: 22px; border-radius: 6px; background-color: #e2e8f0;">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: {{ ($jml / $maks) * 100 }}%; font-weight: bold; background: linear-gradient(90deg, #004b87 0%, #0284c7 100%) !important;">
                        {{ $jml > 0 ? $jml . ' Kejadian' : '' }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Tabel Laporan Terbaru --}}
<div class="card card-pusri">
    <div class="card-header header-accent d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i> Laporan Insiden Terbaru</span>
        <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-link text-primary text-decoration-none">
            Lihat Semua <i class="bi bi-chevron-right"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Jenis Insiden</th>
                    <th>Area Pabrik</th>
                    <th>Waktu Kejadian</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($terbaru as $i)
                <tr>
                    <td>
                        <a href="{{ route('incidents.show', $i) }}" class="fw-bold text-primary text-decoration-none">
                            {{ $i->kode }}
                        </a>
                    </td>
                    <td>{{ $i->type->nama }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $i->unit->nama }}</span></td>
                    <td class="small">{{ $i->tanggal_kejadian->format('d/m/Y H:i') }} WIB</td>
                    <td><span class="badge bg-{{ $i->statusColor() }}">{{ $i->statusLabel() }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('incidents.show', $i) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada laporan insiden saat ini.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
