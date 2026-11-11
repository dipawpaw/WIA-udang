@extends('layouts.dashboard')

@section('title', 'Buat Laporan')
@section('active', 'aduin')

@section('content')
{{-- ==========================================================================
     DATA STATIS — daftar kategori & tahap. Saat backend siap, $categories bisa
     diambil dari tabel kategori. Nilai 'key' dikirim sebagai field "category".
     ========================================================================== --}}
@php
    $categories = [
        ['key' => 'infrastruktur', 'label' => 'Infrastruktur',  'icon' => 'road',      'desc' => 'Jalan, jembatan, trotoar'],
        ['key' => 'penerangan',    'label' => 'Penerangan',     'icon' => 'lightbulb', 'desc' => 'Lampu jalan dan PJU'],
        ['key' => 'transportasi',  'label' => 'Transportasi',   'icon' => 'bus',       'desc' => 'Halte, rambu, angkutan umum'],
        ['key' => 'kebersihan',    'label' => 'Kebersihan',     'icon' => 'trash',     'desc' => 'Sampah dan fasilitas umum'],
        ['key' => 'drainase',      'label' => 'Air & Drainase', 'icon' => 'droplets',  'desc' => 'Selokan, genangan, pipa'],
        ['key' => 'lainnya',       'label' => 'Lainnya',        'icon' => 'plus',      'desc' => 'Masalah publik lain'],
    ];
    $steps = ['Kategori', 'Foto', 'Lokasi', 'Detail', 'Tinjau'];
@endphp

<div id="aduin" class="ad">

    <header class="ad-head">
        <h1 class="ad-head__title">Buat Laporan</h1>
        <p class="ad-head__meta"><x-icon name="clock" :size="16" /> Estimasi 2 menit. Laporan langsung diteruskan ke dinas terkait.</p>
    </header>

    {{-- Indikator tahap --}}
    <nav class="dash-card ad-steps-card" aria-label="Tahapan laporan" data-ad-hide-done>
        <ol class="ad-steps">
            @foreach ($steps as $i => $label)
                <li class="ad-step" data-ad-step="{{ $i + 1 }}">
                    <button type="button" class="ad-step__btn" data-ad-goto="{{ $i + 1 }}">
                        <span class="ad-step__dot"><span class="ad-step__num">{{ $i + 1 }}</span><x-icon name="check" :size="16" /></span>
                        <span class="ad-step__label">{{ $label }}</span>
                    </button>
                </li>
            @endforeach
        </ol>
    </nav>

    <div class="ad-layout">
        <div class="ad-main">

            {{-- Form: action="#" sementara. Ganti ke route penyimpanan laporan (POST) saat backend siap. --}}
            <form id="aduinForm" method="POST" action="#" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- 1. Kategori --}}
                <section class="dash-card ad-panel" data-ad-panel="1" aria-labelledby="adT1">
                    <h2 class="ad-panel__title" id="adT1" tabindex="-1"><span class="ad-panel__num">1</span> Pilih kategori</h2>
                    <p class="ad-panel__help">Pilih yang paling sesuai dengan masalah yang kamu temui.</p>
                    <div class="ad-cats" role="radiogroup" aria-labelledby="adT1">
                        @foreach ($categories as $cat)
                            <label class="ad-cat">
                                <input type="radio" class="visually-hidden" name="category" value="{{ $cat['key'] }}" data-label="{{ $cat['label'] }}">
                                <x-icon :name="$cat['icon']" :size="26" />
                                <strong>{{ $cat['label'] }}</strong>
                                <small>{{ $cat['desc'] }}</small>
                            </label>
                        @endforeach
                    </div>
                    <p class="cz-error" role="alert" data-ad-error="category" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                </section>

                {{-- 2. Foto --}}
                <section class="dash-card ad-panel" data-ad-panel="2" aria-labelledby="adT2" hidden>
                    <h2 class="ad-panel__title" id="adT2" tabindex="-1"><span class="ad-panel__num">2</span> Foto bukti</h2>
                    <p class="ad-panel__help">Unggah 1 sampai 3 foto agar petugas bisa menilai kondisi di lapangan.</p>
                    <label class="ad-drop" data-ad-drop>
                        <input type="file" class="visually-hidden" id="adPhotos" name="photos[]" accept="image/jpeg,image/png" multiple>
                        <x-icon name="upload" :size="24" />
                        <strong>Unggah atau seret foto</strong>
                        <small>JPG atau PNG, maks. 10 MB per foto (maks. 3 foto)</small>
                    </label>
                    <ul class="ad-thumbs" data-ad-thumbs aria-label="Foto yang dipilih"></ul>
                    <p class="cz-error" role="alert" data-ad-error="photos" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                    <ul class="ad-guide">
                        <li>Ambil dari jarak 2 sampai 3 meter agar kerusakan terlihat jelas.</li>
                        <li>Pastikan pencahayaan cukup dan objek tidak buram.</li>
                        <li>Hindari memotret wajah orang lain.</li>
                    </ul>
                </section>

                {{-- 3. Lokasi --}}
                <section class="dash-card ad-panel" data-ad-panel="3" aria-labelledby="adT3" hidden>
                    <h2 class="ad-panel__title" id="adT3" tabindex="-1"><span class="ad-panel__num">3</span> Lokasi kejadian</h2>
                    <p class="ad-panel__help">Tulis alamat atau patokan, lalu tandai titiknya di peta (opsional).</p>

                    {{-- Peta ilustrasi: belum terhubung ke peta/geocoding sungguhan. map_x & map_y = posisi pin dalam persen. --}}
                    <div class="ad-map" data-ad-map tabindex="0" role="application" aria-label="Peta ilustrasi. Klik, atau tekan Enter lalu tombol panah, untuk menandai titik.">
                        <svg class="jl-map__svg" viewBox="0 0 600 300" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
                            <circle class="jl-map__park" cx="90" cy="-10" r="70" />
                            <circle class="jl-map__park" cx="520" cy="300" r="90" />
                            <g class="jl-map__roads">
                                <path d="M-20 40 L260 320" />
                                <path d="M200 -20 L560 340" />
                                <path d="M-20 300 L300 -20" />
                                <path d="M280 330 L640 -30" />
                            </g>
                        </svg>
                        <span class="ad-pin" data-ad-pin hidden>
                            <svg viewBox="0 0 30 38" aria-hidden="true" focusable="false"><path d="M15 0C6.7 0 0 6.6 0 14.8 0 25.6 15 38 15 38s15-12.4 15-23.2C30 6.6 23.3 0 15 0z" /><circle cx="15" cy="14.5" r="5.5" fill="#fff" /></svg>
                        </span>
                        <span class="ad-map__hint" data-ad-map-hint>Klik peta untuk menandai titik</span>
                    </div>
                    <input type="hidden" name="map_x" data-ad-mapx>
                    <input type="hidden" name="map_y" data-ad-mapy>

                    <div class="ad-field">
                        <label for="adAddress" class="ad-label">Alamat atau patokan lokasi</label>
                        <div class="ad-input ad-input--icon">
                            <x-icon name="map-pin" :size="18" />
                            <input type="text" id="adAddress" name="address" placeholder="Contoh: Jl. Sabang, Menteng, Jakarta Pusat" maxlength="160" autocomplete="street-address">
                        </div>
                        <p class="cz-error" role="alert" data-ad-error="address" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                    </div>
                    <p class="ad-selected" data-ad-selected hidden><x-icon name="map-pin" :size="16" /> <span><small>Lokasi dipilih</small><strong data-ad-selected-text></strong></span></p>
                </section>

                {{-- 4. Detail --}}
                <section class="dash-card ad-panel" data-ad-panel="4" aria-labelledby="adT4" hidden>
                    <h2 class="ad-panel__title" id="adT4" tabindex="-1"><span class="ad-panel__num">4</span> Detail laporan</h2>
                    <p class="ad-panel__help">Ceritakan apa masalahnya, sejak kapan terjadi, seberapa parah, dan patokan terdekat.</p>

                    <div class="ad-field">
                        <label for="adTitle" class="ad-label">Judul laporan</label>
                        <div class="ad-input">
                            <input type="text" id="adTitle" name="title" placeholder="Contoh: Lubang besar di Jl. Sabang" maxlength="100">
                        </div>
                        <p class="cz-error" role="alert" data-ad-error="title" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                    </div>

                    <div class="ad-field">
                        <div class="ad-label-row">
                            <label for="adDesc" class="ad-label">Deskripsi</label>
                            <span class="ad-counter" data-ad-counter aria-live="off">0 / 500</span>
                        </div>
                        <textarea id="adDesc" name="description" class="ad-input ad-textarea" rows="6" maxlength="500" placeholder="Jelaskan masalah yang kamu temukan..."></textarea>
                        <p class="ad-helper">Minimal 20 karakter. Hindari menulis data pribadi seperti nomor telepon.</p>
                        <p class="cz-error" role="alert" data-ad-error="description" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                    </div>
                </section>

                {{-- 5. Tinjau --}}
                <section class="dash-card ad-panel" data-ad-panel="5" aria-labelledby="adT5" hidden>
                    <h2 class="ad-panel__title" id="adT5" tabindex="-1"><span class="ad-panel__num">5</span> Tinjau laporan</h2>
                    <p class="ad-panel__help">Periksa kembali sebelum dikirim. Pilih Ubah jika ada yang perlu diperbaiki.</p>
                    <dl class="ad-review">
                        <div class="ad-review__row"><dt>Kategori</dt><dd data-ad-rv="category"></dd><button type="button" class="ad-review__edit" data-ad-goto="1"><x-icon name="pencil" :size="14" /> Ubah<span class="visually-hidden"> kategori</span></button></div>
                        <div class="ad-review__row"><dt>Foto</dt><dd><ul class="ad-thumbs ad-thumbs--sm" data-ad-rv-photos></ul></dd><button type="button" class="ad-review__edit" data-ad-goto="2"><x-icon name="pencil" :size="14" /> Ubah<span class="visually-hidden"> foto</span></button></div>
                        <div class="ad-review__row"><dt>Lokasi</dt><dd data-ad-rv="address"></dd><button type="button" class="ad-review__edit" data-ad-goto="3"><x-icon name="pencil" :size="14" /> Ubah<span class="visually-hidden"> lokasi</span></button></div>
                        <div class="ad-review__row"><dt>Judul</dt><dd data-ad-rv="title"></dd><button type="button" class="ad-review__edit" data-ad-goto="4"><x-icon name="pencil" :size="14" /> Ubah<span class="visually-hidden"> judul</span></button></div>
                        <div class="ad-review__row"><dt>Deskripsi</dt><dd class="ad-review__long" data-ad-rv="description"></dd><button type="button" class="ad-review__edit" data-ad-goto="4"><x-icon name="pencil" :size="14" /> Ubah<span class="visually-hidden"> deskripsi</span></button></div>
                    </dl>
                </section>

                {{-- Tombol navigasi --}}
                <div class="dash-card ad-footer" data-ad-hide-done>
                    <p class="ad-footer__note"><x-icon name="shield-check" :size="22" /> <span>Laporanmu diteruskan ke dinas terkait dan bisa dipantau di Aktivitas Saya.</span></p>
                    <div class="ad-footer__actions">
                        <a href="{{ url('/homepage') }}" class="dash-btn dash-btn--ghost">Batal</a>
                        <button type="button" class="dash-btn dash-btn--secondary" data-ad-prev hidden><x-icon name="arrow-left" :size="18" /> Kembali</button>
                        <button type="button" class="dash-btn dash-btn--primary" data-ad-next>Selanjutnya <x-icon name="arrow-right" :size="18" /></button>
                        <button type="submit" class="dash-btn dash-btn--primary" data-ad-submit hidden><x-icon name="send" :size="18" /> Kirim Laporan</button>
                    </div>
                </div>
            </form>

            {{-- Tampil setelah laporan terkirim --}}
            <section class="dash-card ad-success" data-ad-success tabindex="-1" hidden>
                <span class="ad-success__icon"><x-icon name="circle-check" :size="32" /></span>
                <h2 class="ad-success__title">Laporan terkirim</h2>
                <p class="ad-success__text">Terima kasih sudah ikut menjaga kotamu. Petugas akan memverifikasi laporan ini, dan kamu bisa memantau perkembangannya di Aktivitas Saya.</p>
                <p class="ad-success__ticket"><small>Nomor tiket</small><strong data-ad-ticket></strong></p>
                <p class="ad-success__demo"><span class="dash-badge dash-badge--neutral">Mode contoh</span> Laporan ini belum disimpan ke server.</p>
                <div class="ad-success__actions">
                    <button type="button" class="dash-btn dash-btn--secondary" data-ad-reset>Buat laporan lain</button>
                    <a href="{{ url('/aktivitas') }}" class="dash-btn dash-btn--primary">Lihat Aktivitas Saya <x-icon name="arrow-right" :size="18" /></a>
                </div>
            </section>
        </div>

        {{-- Ringkasan + tips (kanan, sesuai wireframe) --}}
        <aside class="ad-aside" data-ad-hide-done aria-label="Ringkasan laporan">
            <div class="dash-card ad-sum">
                <div class="ad-sum__head">
                    <h2 class="ad-sum__title">Ringkasan</h2>
                    <span class="dash-badge dash-badge--verified" data-ad-sum-count>0 dari 4 terisi</span>
                </div>
                <ul class="ad-sum__list">
                    @foreach ([['category', 'Kategori', 'Belum dipilih'], ['photos', 'Foto', 'Belum ada foto'], ['address', 'Lokasi', 'Belum diisi'], ['detail', 'Deskripsi', 'Belum diisi']] as [$key, $label, $empty])
                        <li class="ad-sum__item" data-ad-sum="{{ $key }}" data-empty="{{ $empty }}">
                            <span class="ad-sum__ico"><x-icon name="clock" :size="18" class="is-todo" /><x-icon name="check" :size="18" class="is-ok" /></span>
                            <span class="ad-sum__text"><small>{{ $label }}</small><strong data-ad-sum-value>{{ $empty }}</strong></span>
                        </li>
                    @endforeach
                </ul>
                <button type="button" class="dash-btn dash-btn--primary ad-sum__btn" data-ad-review disabled><x-icon name="send" :size="18" /> Tinjau &amp; Kirim</button>
                <p class="ad-sum__hint" data-ad-sum-hint>Lengkapi semua tahap untuk melanjutkan.</p>
            </div>

            <div class="ad-tip">
                <x-icon name="lightbulb" :size="18" />
                <div><strong>Tips foto yang baik</strong><p>Ambil dari jarak 2 sampai 3 meter dan pastikan kerusakan terlihat jelas.</p></div>
            </div>
        </aside>
    </div>
</div>
@endsection