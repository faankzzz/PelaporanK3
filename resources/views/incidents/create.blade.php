@extends('layouts.app')
@section('title', 'Buat Laporan Insiden K3')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-exclamation text-warning fs-3"></i>
                    Formulir Laporan Insiden K3
                </h4>
                <p class="text-muted small mb-0">PT Pupuk Sriwidjaja Palembang &bull; Laporkan insiden, near miss, atau bahaya kerja segera.</p>
            </div>
            <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card card-pusri shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data" id="incidentForm">
                    @include('incidents._form')

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Data Anda tersimpan aman dan langsung diteruskan ke tim Petugas K3 Pusri.
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-pusri px-4">
                                <i class="bi bi-send-fill me-1"></i> Kirim Laporan Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
