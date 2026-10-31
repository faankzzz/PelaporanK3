@extends('layouts.app')
@section('title', $user->exists ? 'Ubah Akun' : 'Tambah Akun')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-dark mb-0">{{ $user->exists ? 'Ubah Akun Karyawan' : 'Tambah Akun Baru' }}</h4>
                <p class="text-muted small mb-0">Atur hak akses peran dan unit kerja PT Pusri.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card card-pusri shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @if($user->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Perusahaan *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" name="nip" class="form-control" placeholder="Contoh: 198501012010121001" value="{{ old('nip', $user->nip) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Peran Akses *</label>
                            <select name="role" class="form-select" required>
                                @foreach(\App\Models\User::ROLES as $k => $l)
                                    <option value="{{ $k }}" @selected(old('role', $user->role) === $k)>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit / Area Kerja</label>
                            <select name="unit_id" class="form-select">
                                <option value="">-- Tanpa Unit / Umum --</option>
                                @foreach($units as $un)
                                    <option value="{{ $un->id }}" @selected(old('unit_id', $user->unit_id) == $un->id)>{{ $un->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Password {{ $user->exists ? '(Kosongkan bila tidak ingin diubah)' : '*' }}</label>
                            <input type="password" name="password" class="form-control" minlength="8" {{ $user->exists ? '' : 'required' }}>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-pusri px-4">
                            <i class="bi bi-save me-1"></i> {{ $user->exists ? 'Simpan Pembaruan' : 'Buat Akun' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
