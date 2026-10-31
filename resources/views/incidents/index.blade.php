@extends('layouts.app')
@section('title', 'Daftar Laporan Insiden K3')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-journal-text text-primary"></i> Daftar Laporan Insiden K3
        </h4>
        <p class="text-muted small mb-0">PT Pupuk Sriwidjaja Palembang &bull; Arsip laporan dan monitoring investigasi.</p>
    </div>
    <div>
        <a href="{{ route('incidents.create') }}" class="btn btn-pusri-yellow d-flex align-items-center gap-2">
            <i class="bi bi-camera-fill"></i> + Buat Laporan Cepat
        </a>
    </div>
</div>

<div class="card card-pusri mb-4">
    <div class="card-body p-3">
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode / lokasi / kronologi...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    @foreach(\App\Models\Incident::STATUS as $k => [$label])
                        <option value="{{ $k }}" @selected(request('status') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">-- Semua Jenis Insiden --</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" @selected(request('type') == $t->id)>{{ $t->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-pusri w-100"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                @if(request()->anyFilled(['q', 'status', 'type']))
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary" title="Reset filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card card-pusri shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Waktu Kejadian</th>
                    <th>Jenis Insiden</th>
                    <th>Area Pabrik</th>
                    <th>Pelapor</th>
                    <th>Foto</th>
                    <th>Keparahan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($incidents as $i)
                <tr>
                    <td>
                        <a href="{{ route('incidents.show', $i) }}" class="fw-bold text-primary text-decoration-none">
                            {{ $i->kode }}
                        </a>
                    </td>
                    <td class="small">{{ $i->tanggal_kejadian->format('d/m/Y H:i') }} WIB</td>
                    <td>{{ $i->type->nama }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $i->unit->nama }}</span></td>
                    <td>
                        <div class="small fw-semibold">{{ $i->reporter->name }}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">NIP: {{ $i->reporter->nip ?: '-' }}</div>
                    </td>
                    <td>
                        @if($i->foto)
                            <a href="{{ asset('storage/' . $i->foto) }}" target="_blank" title="Lihat Foto Kamera Langsung">
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-camera-fill"></i> Foto Bukti
                                </span>
                            </a>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $i->severityColor() }}">{{ $i->severityLabel() }}</span></td>
                    <td><span class="badge bg-{{ $i->statusColor() }}">{{ $i->statusLabel() }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('incidents.show', $i) }}" class="btn btn-sm btn-outline-primary">
                            Buka <i class="bi bi-arrow-right"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        Tidak ada laporan insiden yang sesuai kriteria pencarian.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $incidents->links('pagination::bootstrap-5') }}</div>
@endsection
