@extends('layouts.app')
@section('title', 'Ubah Laporan ' . $incident->kode)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-dark mb-0">Ubah Laporan {{ $incident->kode }}</h4>
                <p class="text-muted small mb-0">Hanya dapat diubah selama belum diverifikasi oleh Petugas K3.</p>
            </div>
            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Batal &amp; Kembali
            </a>
        </div>

        <div class="card card-pusri shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('incidents.update', $incident) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('incidents._form')

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-pusri px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
