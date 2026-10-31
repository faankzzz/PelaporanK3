<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SI-K3 PT Pupuk Sriwidjaja Palembang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/pusri.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #0b2e59;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-head {
            background-color: #003362;
            color: #ffffff;
            padding: 1.5rem 1.5rem 1.25rem;
            text-align: center;
            border-bottom: 3px solid #f5a623;
        }
    </style>
</head>
<body>
<div class="container py-4 d-flex justify-content-center">
    <div class="login-box">
        <div class="login-head">
            <h5 class="fw-bold mb-0">SI-K3 PUSRI</h5>
            <div style="font-size: 0.75rem; color: #ffd27f; font-weight: 600; letter-spacing: 0.5px; margin-top: 2px;">
                PT PUPUK SRIWIDJAJA PALEMBANG
            </div>
            <p class="text-white-50 small mb-0 mt-2">Sistem Pelaporan Insiden &amp; Bahaya Kerja</p>
        </div>

        <div class="p-4">
            @if($errors->has('email'))
                <div class="alert alert-danger py-2 small mb-3">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email Pengguna</label>
                    <input type="email" name="email" id="emailInput" value="{{ old('email') }}"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           placeholder="Masukkan email anda" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" id="passwordInput" class="form-control form-control-sm"
                           placeholder="Password" required>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Ingat akun saya</label>
                </div>

                <button class="btn btn-pusri-yellow w-100 py-2 fw-bold">
                    Masuk
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center">
                <span class="text-muted small d-block mb-2">Klik untuk isi Akun cepat:</span>
                <div class="d-flex justify-content-center gap-1 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillDemo('pelapor@k3.test')">
                        Pelapor
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillDemo('k3@k3.test')">
                        Petugas K3
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" onclick="fillDemo('admin@k3.test')">
                        Admin
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillDemo(email) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = 'password123';
    }
</script>
</body>
</html>
