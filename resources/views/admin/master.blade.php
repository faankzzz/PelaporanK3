@extends('layouts.app')
@section('title', 'Master Data K3 Pusri')

@section('content')
<div class="mb-3">
    <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-database-fill text-primary"></i> Master Data K3
    </h4>
    <p class="text-muted small mb-0">Kelola daftar unit/area pabrik Pusri dan jenis insiden kerja.</p>
</div>

<div class="row g-4">
    @foreach(['unit' => ['Unit / Area Pabrik Pusri', $units, 'bi-building-fill'], 'jenis' => ['Jenis Insiden Kerja', $types, 'bi-tags-fill']] as $tabel => [$judul, $rows, $icon])
    <div class="col-md-6">
        <div class="card card-pusri h-100">
            <div class="card-header header-accent">
                <span><i class="bi {{ $icon }} text-primary me-2"></i> {{ $judul }}</span>
                <span class="badge bg-secondary-subtle text-dark">{{ count($rows) }} item</span>
            </div>
            <ul class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                @foreach($rows as $r)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="fw-semibold text-dark">{{ $r->nama }}</span>
                        <form method="POST" action="{{ route('admin.master.destroy', [$tabel, $r->id]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <div class="card-body border-top bg-light">
                <form method="POST" action="{{ route('admin.master.store', $tabel) }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Tambah {{ strtolower($judul) }} baru..." required>
                    <button class="btn btn-pusri btn-sm text-nowrap">+ Tambah</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mt-4">
    <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
    <div>
        <strong>Catatan:</strong> Tingkat keparahan (<em>Ringan, Sedang, Berat, Fatal</em>) distandarisasi secara global berdasarkan Pedoman K3 Pusri pada model <code>Incident</code>.
    </div>
</div>
@endsection
