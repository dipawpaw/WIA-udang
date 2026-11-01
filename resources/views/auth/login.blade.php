<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOGIN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="auth-page">
        <a href="/" class="auth-brand">CITYZEN</a>

        <div class="auth-card">
            <h1 class="auth-card__title">Masuk ke Cityzen</h1>
            <p class="auth-card__subtitle">Pantau laporanmu dan lihat progres penanganannya.</p>

            <form action="#" method="POST" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" autocomplete="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn-cityzen">Masuk</button>
            </form>

            <p class="auth-card__switch">Belum punya akun? <a href="/register">Daftar</a></p>
        </div>
    </main>
</body>
</html>