@extends('layouts.app')
@section('title', 'Kelola Akun')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-primary"></i> Kelola Akun Karyawan &amp; Petugas
        </h4>
        <p class="text-muted small mb-0">Manajemen pengguna sistem K3 PT Pupuk Sriwidjaja Palembang.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-pusri-yellow">
        <i class="bi bi-person-plus-fill me-1"></i> + Tambah Akun
    </a>
</div>

<div class="card card-pusri shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Email Perusahaan</th>
                    <th>NIP</th>
                    <th>Unit / Departemen</th>
                    <th>Peran</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
                <tr>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $u->nip ?: '-' }}</span></td>
                    <td>{{ $u->unit->nama ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $u->isAdmin() ? 'bg-danger' : ($u->isK3() ? 'bg-warning text-dark' : 'bg-primary') }}">
                            {{ $u->roleLabel() }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Ubah
                        </a>
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
@endsection
