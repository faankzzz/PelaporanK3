@extends('layouts.app')
@section('title', 'Audit Log Sistem K3')

@section('content')
<div class="mb-3">
    <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i> Audit Log Aktivitas Sistem
    </h4>
    <p class="text-muted small mb-0">Rekam jejak seluruh aktivitas pelaporan, verifikasi, login, dan modifikasi data K3 Pusri.</p>
</div>

<div class="card card-pusri shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Waktu (WIB)</th>
                    <th>Pengguna</th>
                    <th>Kode Aksi</th>
                    <th>Keterangan Aktivitas</th>
                    <th>Alamat IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $l)
                <tr>
                    <td class="text-nowrap small font-monospace">{{ $l->created_at->format('d/m/Y H:i:s') }}</td>
                    <td><strong>{{ $l->user->name ?? 'Sistem' }}</strong></td>
                    <td><span class="badge bg-secondary font-monospace">{{ $l->aksi }}</span></td>
                    <td class="small">{{ $l->deskripsi }}</td>
                    <td class="small text-muted font-monospace">{{ $l->ip ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada catatan aktivitas di log.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $logs->links('pagination::bootstrap-5') }}</div>
@endsection
