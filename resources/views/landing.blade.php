<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cityzen</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg cz-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">Cityzen</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#czNav" aria-controls="czNav" aria-expanded="false" aria-label="Buka menu">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div class="collapse navbar-collapse" id="czNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="/">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#cara-kerja">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link" href="#public-pulse">Public Pulse</a></li>
                </ul>
                <a class="nav-login ms-lg-auto px-lg-0 px-3 py-2" href="/login">Masuk</a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <header class="hero">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <h1 class="hero__title">Ada yang rusak di sekitarmu? Laporkan, lalu pantau sampai beres.</h1>
                    <p class="hero__desc">Foto, kirim, dan lihat sendiri laporanmu sudah sampai tahap mana di dinas terkait. Tingkat kepuasan warga di lingkunganmu juga bisa dipantau lewat Public Pulse.</p>
                    <div class="hero__actions">
                        <a href="/register" class="btn-cityzen">Mulai melapor →</a>
                        <a href="#fitur" class="btn-cityzen-soft">Pelajari lebih lanjut</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero__media">
                        <img src="{{ asset('images/hero.jpeg') }}" alt="Foto kota">
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Fitur --}}
    <section class="section section--tint" id="fitur">
        <div class="container">
            <div class="section-head">
                <h2 class="section-title">Apa saja yang bisa kamu lakukan di Cityzen</h2>
                <p class="section-subtitle">Empat fitur dalam satu aplikasi, supaya dinas bisa menanggapi laporan warga dengan lebih cepat dan terukur.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <article class="cz-card feature-card">
                        <h3 class="feature-card__title">Aduin</h3>
                        <p class="feature-card__text">Lihat jalan berlubang atau fasilitas rusak? Foto, tandai lokasinya lewat GPS, lalu kirim. Cukup beberapa menit.</p>
                        <a href="/register" class="feature-card__link">Kirim keluhan →</a>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="cz-card feature-card">
                        <h3 class="feature-card__title">Jelajahi</h3>
                        <p class="feature-card__text">Cek peta aduan di sekitarmu. Kalau masalahnya sama, dukung laporan yang sudah ada dan lihat isu mana yang jadi prioritas wilayahmu.</p>
                        <a href="/login" class="feature-card__link">Buka peta interaktif →</a>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="cz-card feature-card">
                        <h3 class="feature-card__title">Aktivitas Saya</h3>
                        <p class="feature-card__text">Semua laporan yang pernah kamu kirim ada di sini beserta statusnya. Dinas menanggapi? Kamu langsung dapat notifikasi.</p>
                        <a href="/login" class="feature-card__link">Pantau histori →</a>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="cz-card feature-card">
                        <h3 class="feature-card__title">Wawasan Kota</h3>
                        <p class="feature-card__text">Seberapa cepat dinas menyelesaikan laporan, dan berapa skor kepuasan warganya? Datanya terbuka untuk dilihat.</p>
                        <a href="#public-pulse" class="feature-card__link">Lihat analitik →</a>
                    </article>
                </div>
            </div>
        </div>
    </section>

    {{-- Cara Kerja --}}
    <section class="section" id="cara-kerja">
        <div class="container">
            <div class="section-head">
                <h2 class="section-title">Tiga langkah, dari lapor sampai beres</h2>
                <p class="section-subtitle">Alurnya sederhana dan bisa kamu ikuti sendiri.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step__num">1</div>
                    <h3 class="step__title">Buat laporan</h3>
                    <p class="step__text">Foto masalahnya, pilih kategori dan lokasi, lalu kirim. Laporanmu langsung masuk ke meja kendali dinas.</p>
                </div>
                <div class="col-md-4">
                    <div class="step__num">2</div>
                    <h3 class="step__title">Pantau progres</h3>
                    <p class="step__text">Dinas terkait memperbarui status secara berkala dan menampilkan bukti penanganan di lapangan.</p>
                </div>
                <div class="col-md-4">
                    <div class="step__num">3</div>
                    <h3 class="step__title">Konfirmasi selesai</h3>
                    <p class="step__text">Cek sendiri apakah perbaikannya sudah benar, lalu beri nilai untuk layanan yang kamu terima.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Public Pulse --}}
    <section class="section section--tint" id="public-pulse">
        <div class="container">
            <div class="section-head d-flex flex-wrap justify-content-between align-items-end gap-2">
                <div>
                    <h2 class="section-title">Public Pulse</h2>
                    <p class="section-subtitle">Penilaian warga soal kualitas lingkungan dan layanan publik, terbuka untuk siapa saja.</p>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="cz-card stat-card">
                        <div class="stat-card__label">Rating rata-rata</div>
                        <div class="stat-card__value">4.6<span class="stat-card__unit">/5.0</span></div>
                        <p class="stat-card__desc">Kepuasan kota bulan ini</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cz-card stat-card">
                        <div class="stat-card__label">Distribusi rating</div>
                        <div class="stat-card__value">88%<span class="stat-card__unit">positif</span></div>
                        <p class="stat-card__desc">Dari 12.450 warga yang ikut menilai</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cz-card stat-card">
                        <div class="stat-card__label">Tren isu wilayah</div>
                        <div class="stat-card__value">94%<span class="stat-card__unit">selesai</span></div>
                        <p class="stat-card__desc">Jalan dan infrastruktur paling cepat ditangani</p>
                    </div>
                </div>
            </div>

            <div class="cz-card index-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="index-card__eyebrow">Indeks Kepuasan Lingkungan 2025</div>
                        <h3 class="index-card__title">Nilai RT/RW dan kelurahanmu</h3>
                        <p class="index-card__text">Setiap penilaian diverifikasi lewat NIK dan lokasi, supaya hasilnya tidak bisa dimanipulasi dan tetap adil.</p>
                        <p class="index-card__quote-by"><strong>Rian Ardiansyah</strong> · Menteng, Jakarta</p>
                        <p class="index-card__quote">“Laporan lubang jalan di gang kami selesai dalam 48 jam. Transparansi dinasnya terasa banget!”</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="rating-box">
                            <div class="rating-box__title">Kasih nilai untuk wilayahmu</div>
                            <div class="rating-box__stars" role="radiogroup" aria-label="Beri nilai bintang">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" class="rating-star" data-value="{{ $i }}" aria-label="{{ $i }} bintang"><i class="bi bi-star-fill"></i></button>
                                @endfor
                            </div>
                            <p class="rating-box__hint">Ketuk bintang untuk memberi skor</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta">
        <div class="container">
            <div class="cta__box">
                <div class="cta__eyebrow">Gerakan sipil digital terbuka</div>
                <h2 class="cta__title">Yuk, bikin kotamu lebih nyaman dan sigap</h2>
                <p class="cta__text">Gabung bersama ribuan warga lain yang ikut menjaga lingkungannya tetap transparan, aman, dan ramah untuk semua orang di seluruh Indonesia.</p>
                <div class="cta__actions">
                    <a href="/register" class="btn-cityzen-light">Daftar sekarang</a>
                    <a href="/login" class="btn-cityzen-ghost">Masuk akun</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="cz-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="cz-footer__brand">Cityzen <small>· Civic Tech RI</small></div>
                    <p class="cz-footer__desc">Platform aspirasi warga yang bersertifikasi kepatuhan data publik dan transparansi tata kelola wilayah.</p>
                    <p class="cz-footer__copy">© 2024 Cityzen. Seluruh hak cipta dilindungi undang-undang.</p>
                </div>
                <div class="col-lg-5">
                    <nav class="cz-footer__links" aria-label="Footer">
                        <a href="#">Tentang Kami</a>
                        <a href="#">Kebijakan Privasi</a>
                        <a href="#">Transparansi</a>
                        <a href="#">Kontak Warga</a>
                    </nav>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>