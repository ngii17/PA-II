<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard - Purnama Hotel & Resto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-primary { background: #1a1a2e; border: none; }
        .btn-primary:hover { background: #16213e; }
    </style>
</head>
<body>

<div class="login-card card p-4">
    <div class="text-center mb-4">
        <h4 class="fw-bold">🏨 Purnama Dashboard</h4>
        <p class="text-muted small">Silakan login untuk mengelola sistem</p>
    </div>

    {{-- Tampilkan Error jika login gagal --}}
    @if($errors->any())
        <div class="alert alert-danger py-2 small">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('dashboard.login.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-bold">Alamat Email</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Masuk ke Dashboard</button>
    </form>

    <div class="text-center mt-4">
        <p class="text-muted small">© {{ date('Y') }} Purnama Hotel & Resto</p>
    </div>
</div>

</body>
</html>
