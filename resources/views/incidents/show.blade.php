@extends('layouts.app')
@section('title', $incident->kode)

@section('content')
@php $u = auth()->user(); @endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h3 class="fw-bold text-dark mb-0">{{ $incident->kode }}</h3>
            <span class="badge bg-{{ $incident->statusColor() }} px-3 py-2 fs-6">
                {{ $incident->statusLabel() }}
            </span>
            <span class="badge bg-{{ $incident->severityColor() }} px-3 py-2 fs-6">
                Keparahan: {{ $incident->severityLabel() }}
            </span>
        </div>
        <p class="text-muted small mb-0">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Area: <strong>{{ $incident->unit->nama }}</strong> &bull;
            <i class="bi bi-clock me-1"></i> Waktu Kejadian: <strong>{{ $incident->tanggal_kejadian->format('d/m/Y H:i') }} WIB</strong>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($incident->reporter_id === $u->id && $incident->status === 'dilaporkan')
            <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Ubah Laporan
            </a>
        @endif
        <a href="{{ route('incidents.print', $incident) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak Laporan
        </a>
        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

{{-- Alur Status Progresif Pusri --}}
<div class="card card-pusri mb-4 border-0">
    <div class="card-body p-3">
        <div class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing: 0.5px;">Proses Penanganan Insiden K3 Pusri:</div>
        <div class="d-flex flex-wrap gap-2">
            @php
                $urut = array_keys(\App\Models\Incident::STATUS);
                $idx = array_search($incident->status, $urut);
            @endphp
            @foreach(\App\Models\Incident::STATUS as $k => [$label])
                @php $stepPassed = array_search($k, $urut) <= $idx; @endphp
                <span class="badge rounded-pill px-3 py-2 {{ $stepPassed ? 'bg-primary' : 'bg-light text-muted border' }}"
                      style="{{ $stepPassed ? 'background: linear-gradient(135deg, #00264d 0%, #004b87 100%) !important;' : '' }}">
                    @if($stepPassed) <i class="bi bi-check2-circle text-warning me-1"></i> @endif
                    {{ $loop->iteration }}. {{ $label }}
                </span>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        {{-- Data Rincian Insiden --}}
        <div class="card card-pusri mb-4">
            <div class="card-header header-accent">
                <span><i class="bi bi-file-earmark-text text-primary me-2"></i> Rincian Informasi Insiden</span>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Jenis Kejadian</dt>
                    <dd class="col-sm-8 fw-semibold text-dark">{{ $incident->type->nama }}</dd>

                    <dt class="col-sm-4 text-muted">Unit / Area Pabrik</dt>
                    <dd class="col-sm-8 text-dark"><span class="badge bg-light text-dark border">{{ $incident->unit->nama }}</span></dd>

                    <dt class="col-sm-4 text-muted">Lokasi Spesifik</dt>
                    <dd class="col-sm-8 text-dark">{{ $incident->lokasi_detail }}</dd>

                    <dt class="col-sm-4 text-muted">Waktu Pelaporan</dt>
                    <dd class="col-sm-8 text-dark">{{ $incident->tanggal_kejadian->format('d/m/Y H:i') }} WIB</dd>

                    <dt class="col-sm-4 text-muted">Pelapor</dt>
                    <dd class="col-sm-8 text-dark">
                        <strong>{{ $incident->reporter->name }}</strong>
                        <span class="text-muted small">(NIP: {{ $incident->reporter->nip ?: '-' }})</span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Korban Cedera</dt>
                    <dd class="col-sm-8 text-dark">{{ $incident->korban ?: 'Tidak Ada Korban' }}</dd>

                    <dt class="col-sm-4 text-muted">Saksi di Tempat</dt>
                    <dd class="col-sm-8 text-dark">{{ $incident->saksi ?: '-' }}</dd>

                    <dt class="col-sm-12 text-muted mt-2">Kronologi / Penjelasan Kejadian</dt>
                    <dd class="col-sm-12 text-dark bg-light p-3 rounded mt-1 border">
                        {!! nl2br(e($incident->kronologi)) !!}
                    </dd>
                </dl>
            </div>
        </div>

        {{-- Data Verifikasi --}}
        @if($incident->verified_at)
        <div class="card card-pusri mb-4">
            <div class="card-header header-accent">
                <span><i class="bi bi-shield-check text-info me-2"></i> Hasil Verifikasi Petugas K3</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-person-check-fill text-success fs-5"></i>
                    <div>
                        Diverifikasi oleh <strong>{{ $incident->verifier->name ?? '-' }}</strong> pada {{ $incident->verified_at->format('d/m/Y H:i') }} WIB.
                    </div>
                </div>
                <div class="bg-light p-3 rounded border">
                    <div class="small fw-bold text-muted mb-1">Catatan Verifikasi:</div>
                    <div>{{ $incident->catatan_verifikasi ?: '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Form Verifikasi oleh Petugas K3 --}}
        @if($u->isK3() && $incident->status === 'dilaporkan')
        <div class="card card-pusri mb-4 border-warning">
            <div class="card-header bg-warning-subtle text-dark fw-bold">
                <i class="bi bi-shield-exclamation me-1"></i> Form Verifikasi Laporan (Khusus Petugas K3)
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('k3.verify', $incident) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Tentukan Tingkat Keparahan *</label>
                            <select name="severity" class="form-select border-warning" required>
                                <option value="">-- Pilih Keparahan --</option>
                                @foreach(\App\Models\Incident::SEVERITY as $k => [$label])
                                    <option value="{{ $k }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Catatan Verifikasi Lapangan</label>
                            <input type="text" name="catatan_verifikasi" class="form-control" placeholder="Catatan awal pemeriksaan petugas...">
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-pusri-yellow">
                                <i class="bi bi-check-lg me-1"></i> Simpan Verifikasi &amp; Lanjut Investigasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Kolom Kanan: Foto Bukti Kamera & Status --}}
    <div class="col-lg-4">
        {{-- Kartu Foto Bukti Kamera Langsung --}}
        <div class="card card-pusri mb-4">
            <div class="card-header header-accent">
                <span><i class="bi bi-camera-fill text-warning me-2"></i> Bukti Foto Kamera</span>
            </div>
            <div class="card-body text-center p-3">
                @if($incident->foto)
                    <div class="position-relative border rounded overflow-hidden shadow-sm mb-2">
                        <a href="{{ asset('storage/' . $incident->foto) }}" target="_blank">
                            <img src="{{ asset('storage/' . $incident->foto) }}" class="img-fluid w-100" style="max-height: 280px; object-fit: cover;">
                        </a>
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white py-1 small">
                            <i class="bi bi-shield-lock-fill text-warning me-1"></i> Foto Langsung dari Kamera
                        </div>
                    </div>
                    <div class="small text-muted">
                        <a href="{{ asset('storage/' . $incident->foto) }}" target="_blank" class="text-primary text-decoration-none">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Gambar Resolusi Penuh
                        </a>
                    </div>
                @else
                    <div class="p-4 bg-light rounded text-muted">
                        <i class="bi bi-camera-video-off fs-1 d-block mb-2 text-secondary"></i>
                        <span class="small">Tidak ada foto bukti kamera yang dilampirkan.</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Info Card --}}
        <div class="card card-pusri">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-shield-fill-check text-primary fs-3"></i>
                    <div>
                        <div class="fw-bold text-dark">K3 PUSRI SAFE</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Sistem Pelaporan Otentik</div>
                    </div>
                </div>
                <div class="small text-muted">
                    Laporan ini tercatat di database sentral K3 Pusri. Setiap proses verifikasi, investigasi, dan tindakan korektif diaudit secara otomatis.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- INVESTIGASI --}}
@if($incident->status !== 'dilaporkan')
<div class="card card-pusri mb-4">
    <div class="card-header header-accent">
        <span><i class="bi bi-search text-warning me-2"></i> Hasil Investigasi Petugas K3</span>
    </div>
    <div class="card-body">
        @if($u->isK3() && in_array($incident->status, ['diverifikasi','investigasi']))
            <form method="POST" action="{{ route('k3.investigation', $incident) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Penyebab Langsung (Direct Cause)</label>
                        <textarea name="penyebab_langsung" rows="3" class="form-control" placeholder="Tindakan tidak aman / kondisi tidak aman...">{{ old('penyebab_langsung', $incident->penyebab_langsung) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Penyebab Dasar (Root Cause)</label>
                        <textarea name="penyebab_dasar" rows="3" class="form-control" placeholder="Faktor personal / faktor pekerjaan...">{{ old('penyebab_dasar', $incident->penyebab_dasar) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Uraian Lengkap Hasil Investigasi</label>
                        <textarea name="hasil_investigasi" rows="3" class="form-control" placeholder="Rangkuman investigasi tim K3...">{{ old('hasil_investigasi', $incident->hasil_investigasi) }}</textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-pusri">
                            <i class="bi bi-save me-1"></i> Simpan Hasil Investigasi
                        </button>
                    </div>
                </div>
            </form>
        @else
            <dl class="row mb-0">
                <dt class="col-sm-3 text-muted">Penyebab Langsung</dt>
                <dd class="col-sm-9">{!! nl2br(e($incident->penyebab_langsung ?: '-')) !!}</dd>
                <dt class="col-sm-3 text-muted">Penyebab Dasar</dt>
                <dd class="col-sm-9">{!! nl2br(e($incident->penyebab_dasar ?: '-')) !!}</dd>
                <dt class="col-sm-3 text-muted">Hasil Investigasi</dt>
                <dd class="col-sm-9">{!! nl2br(e($incident->hasil_investigasi ?: '-')) !!}</dd>
            </dl>
        @endif
    </div>
</div>
@endif

{{-- TINDAKAN KOREKTIF & PREVENTIF --}}
@if(in_array($incident->status, ['investigasi','tindak_lanjut','selesai']))
<div class="card card-pusri mb-4">
    <div class="card-header header-accent">
        <span><i class="bi bi-wrench-adjustable-circle text-primary me-2"></i> Tindakan Korektif &amp; Preventif (CAPA)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Rencana Tindakan</th>
                    <th>Penanggung Jawab</th>
                    <th>Tenggat Waktu</th>
                    <th>Status Tindakan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($incident->actions as $a)
                <tr>
                    <td>{!! nl2br(e($a->deskripsi)) !!}</td>
                    <td><strong>{{ $a->penanggung_jawab }}</strong></td>
                    <td>{{ $a->tenggat->format('d/m/Y') }}</td>
                    <td>
                        @if($u->isK3() && $incident->status !== 'selesai')
                            <form method="POST" action="{{ route('k3.actions.update', $a) }}" class="d-flex gap-1">
                                @csrf @method('PATCH')
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach(['belum' => 'Belum', 'proses' => 'Proses', 'selesai' => 'Selesai'] as $k => $l)
                                        <option value="{{ $k }}" @selected($a->status === $k)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <span class="badge bg-{{ $a->status === 'selesai' ? 'success' : ($a->status === 'proses' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada tindakan korektif yang didaftarkan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($u->isK3() && in_array($incident->status, ['investigasi','tindak_lanjut']))
    <div class="card-body border-top bg-light">
        <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-plus-circle me-1"></i> Tambah Tindakan Baru:</h6>
        <form method="POST" action="{{ route('k3.actions.store', $incident) }}" class="row g-2">
            @csrf
            <div class="col-md-5">
                <input type="text" name="deskripsi" class="form-control form-control-sm" placeholder="Rencana tindakan perbaikan..." required>
            </div>
            <div class="col-md-3">
                <input type="text" name="penanggung_jawab" class="form-control form-control-sm" placeholder="Penanggung jawab..." required>
            </div>
            <div class="col-md-2">
                <input type="date" name="tenggat" class="form-control form-control-sm" min="{{ now()->toDateString() }}" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-pusri btn-sm w-100">+ Tambah</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endif

{{-- Transisi Status Laporan --}}
@if($u->isK3() && in_array($incident->status, ['investigasi','tindak_lanjut']))
<div class="card card-pusri mb-4 border-primary">
    <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        @if($incident->status === 'investigasi')
            <div>
                <strong>Langkah Selanjutnya:</strong> Setelah mengisi investigasi dan tindakan, lanjutkan status laporan ke <em>Tindak Lanjut</em>.
            </div>
            <form method="POST" action="{{ route('k3.status', $incident) }}">
                @csrf
                <input type="hidden" name="status" value="tindak_lanjut">
                <button class="btn btn-pusri">
                    Lanjut ke Tindak Lanjut <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>
        @else
            <div>
                <strong>Penyelesaian:</strong> Tutup laporan jika seluruh tindakan korektif telah tuntas dilaksanakan.
            </div>
            <form method="POST" action="{{ route('k3.status', $incident) }}" onsubmit="return confirm('Apakah Anda yakin seluruh tindakan telah tuntas dan laporan siap ditutup?')">
                @csrf
                <input type="hidden" name="status" value="selesai">
                <button class="btn btn-success">
                    <i class="bi bi-check-all me-1"></i> Tutup Laporan (Selesai)
                </button>
            </form>
        @endif
    </div>
</div>
@endif

@endsection
