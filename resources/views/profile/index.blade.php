@extends('layouts.dashboard')

@section('title', 'Profil')
@section('active', 'profil')

@section('content')
{{-- ==========================================================================
     DUMMY DATA — hanya blok @php ini yang perlu diganti saat backend siap.
     $user     : data akun pengguna yang sedang login (tanpa NIK, sesuai wireframe).
     $settings : nilai awal preferensi (jika pengguna sudah pernah mengubahnya,
                 JavaScript memakai nilai yang tersimpan di browser).
     ========================================================================== --}}
@php
    $user = [
        'name'      => 'Siti Aulia',
        'username'  => 'siti_aulia',
        'email'     => 'siti.aulia@gmail.com',
        'phone'     => '0812 3456 7890',
        'district'  => 'Menteng, Jakarta Pusat',
        'joined'    => 'Agustus 2025',
        'verified'  => true,
        'stats'     => ['reports' => 6, 'done' => 2, 'process' => 2],
    ];

    $settings = [
        'theme'         => 'terang',   // terang | gelap | otomatis
        'text_size'     => 'normal',   // normal | besar
        'notif_status'  => true,
        'notif_pulse'   => false,
        'notif_info'    => true,
        'show_name'     => false,
    ];

    $themes = [
        ['key' => 'terang',    'label' => 'Terang',   'icon' => 'sun'],
        ['key' => 'gelap',     'label' => 'Gelap',    'icon' => 'moon'],
        ['key' => 'otomatis',  'label' => 'Otomatis', 'icon' => 'sun-moon'],
    ];

    $switches = [
        ['key' => 'notif_status', 'title' => 'Notifikasi status laporan',       'desc' => 'Kabari saat laporanmu berubah status'],
        ['key' => 'notif_pulse',  'title' => 'Ringkasan mingguan Public Pulse', 'desc' => 'Rangkuman kondisi kotamu tiap Senin'],
        ['key' => 'notif_info',   'title' => 'Informasi dan pengumuman penting', 'desc' => 'Kabar layanan dan pengumuman dari kota'],
        ['key' => 'show_name',    'title' => 'Tampilkan nama di laporan publik', 'desc' => 'Jika mati, laporanmu tampil anonim'],
    ];
@endphp

<div id="profil" class="pf" data-logout-url="{{ url('/') }}">

    <header class="pf-head">
        <div>
            <h1 class="pf-head__title">Profil</h1>
            <p class="pf-head__meta">Kelola info pribadi, tampilan, dan pengaturan akunmu.</p>
        </div>
        <span class="dash-badge dash-badge--neutral">Data contoh</span>
    </header>

    {{-- Ringkasan profil --}}
    <section class="dash-card pf-overview" aria-label="Ringkasan profil">
        <div class="pf-overview__who">
            <div class="pf-avatar-wrap">
                <span class="pf-avatar" data-pf-avatar>
                    <span data-pf-avatar-text>
                        {{ collect(preg_split('/\s+/', trim($user['name'])))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') }}
                    </span>
                </span>
                <label class="pf-avatar__btn" title="Ganti foto profil">
                    <x-icon name="camera" :size="14" />
                    <span class="visually-hidden">Ganti foto profil</span>
                    <input type="file" class="visually-hidden" id="pfPhoto" accept="image/jpeg,image/png">
                </label>
            </div>
            <div class="pf-overview__text">
                <div class="pf-overview__name">
                    <h2 data-pf-name>{{ $user['name'] }}</h2>
                    @if ($user['verified'])
                        <span class="dash-badge dash-badge--done"><i></i>Warga Terverifikasi</span>
                    @endif
                </div>
                <p class="pf-overview__meta">
                    <span>&#64;<span data-pf-username>{{ $user['username'] }}</span></span>
                    <span data-pf-district>{{ $user['district'] }}</span>
                    <span>Bergabung sejak {{ $user['joined'] }}</span>
                </p>
            </div>
        </div>
        <dl class="pf-stats">
            <div class="pf-stat"><dt>Laporan</dt><dd>{{ $user['stats']['reports'] }}</dd></div>
            <div class="pf-stat"><dt>Selesai</dt><dd>{{ $user['stats']['done'] }}</dd></div>
            <div class="pf-stat"><dt>Dalam proses</dt><dd>{{ $user['stats']['process'] }}</dd></div>
        </dl>
    </section>

    <div class="pf-grid">

        {{-- Info pribadi. Form: action="#" sementara. Ganti ke route update profil (PATCH) saat backend siap. --}}
        <form class="dash-card pf-card" id="profilForm" method="POST" action="#" novalidate>
            @csrf
            <div class="pf-card__head">
                <h2 class="pf-card__title">Info pribadi</h2>
                <p class="pf-card__note">Data ini tidak ditampilkan di laporan publik.</p>
            </div>

            <div class="pf-fields">
                <div class="ad-field">
                    <label for="pfName" class="ad-label">Nama lengkap</label>
                    <div class="ad-input"><input type="text" id="pfName" name="name" value="{{ $user['name'] }}" autocomplete="name" maxlength="80"></div>
                    <p class="cz-error" role="alert" data-pf-error="name" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                </div>
                <div class="ad-field">
                    <label for="pfUsername" class="ad-label">Nama pengguna</label>
                    <div class="ad-input"><input type="text" id="pfUsername" name="username" value="{{ $user['username'] }}" autocomplete="username" maxlength="20"></div>
                    <p class="cz-error" role="alert" data-pf-error="username" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                </div>
                <div class="ad-field">
                    <label for="pfEmail" class="ad-label">Alamat email</label>
                    <div class="ad-input"><input type="email" id="pfEmail" name="email" value="{{ $user['email'] }}" autocomplete="email" maxlength="120"></div>
                    <p class="cz-error" role="alert" data-pf-error="email" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                </div>
                <div class="ad-field">
                    <label for="pfPhone" class="ad-label">Nomor ponsel</label>
                    <div class="ad-input"><input type="tel" id="pfPhone" name="phone" value="{{ $user['phone'] }}" autocomplete="tel" inputmode="tel" maxlength="20"></div>
                    <p class="cz-error" role="alert" data-pf-error="phone" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                </div>
                <div class="ad-field pf-fields__wide">
                    <label for="pfDistrict" class="ad-label">Kecamatan domisili</label>
                    <div class="ad-input"><input type="text" id="pfDistrict" name="district" value="{{ $user['district'] }}" autocomplete="address-level2" maxlength="100"></div>
                    <p class="cz-error" role="alert" data-pf-error="district" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                </div>
            </div>

            @if ($user['verified'])
                <div class="pf-verified">
                    <x-icon name="shield-check" :size="22" />
                    <div><strong>Identitas terverifikasi</strong><p>Akunmu terhubung dengan Identitas Kependudukan Digital (IKD).</p></div>
                </div>
            @endif

            <div class="pf-card__actions">
                <button type="button" class="dash-btn dash-btn--secondary" data-pf-cancel disabled>Batal</button>
                <button type="submit" class="dash-btn dash-btn--primary" data-pf-save disabled>Simpan perubahan</button>
            </div>
        </form>

        <div class="pf-side">
            {{-- Tampilan --}}
            <section class="dash-card pf-card" aria-labelledby="pfDisplayTitle">
                <h2 class="pf-card__title" id="pfDisplayTitle">Tampilan</h2>

                <fieldset class="pf-fieldset">
                    <legend class="ad-label">Tema</legend>
                    <div class="pf-opts pf-opts--3">
                        @foreach ($themes as $t)
                            <label class="pf-opt">
                                <input type="radio" class="visually-hidden" name="theme" value="{{ $t['key'] }}" data-pf-pref="theme">
                                <x-icon :name="$t['icon']" :size="24" />
                                <strong>{{ $t['label'] }}</strong>
                            </label>
                        @endforeach
                    </div>
                    <p class="pf-note" data-pf-theme-note hidden>Tema gelap belum tersedia di versi ini. Pilihanmu sudah tersimpan dan dipakai saat tema itu dirilis.</p>
                </fieldset>

                <fieldset class="pf-fieldset">
                    <legend class="ad-label">Ukuran teks</legend>
                    <div class="pf-opts pf-opts--2">
                        <label class="pf-opt pf-opt--text">
                            <input type="radio" class="visually-hidden" name="text_size" value="normal" data-pf-pref="text_size">
                            <strong>Normal</strong>
                        </label>
                        <label class="pf-opt pf-opt--text">
                            <input type="radio" class="visually-hidden" name="text_size" value="besar" data-pf-pref="text_size">
                            <strong>Besar</strong>
                        </label>
                    </div>
                </fieldset>
            </section>

            {{-- Pengaturan --}}
            <section class="dash-card pf-card" aria-labelledby="pfSettingsTitle">
                <h2 class="pf-card__title" id="pfSettingsTitle">Pengaturan</h2>
                <ul class="pf-sets">
                    @foreach ($switches as $i => $s)
                        <li class="pf-set">
                            <span class="pf-set__text" id="pfSw{{ $i }}"><strong>{{ $s['title'] }}</strong><small>{{ $s['desc'] }}</small></span>
                            <button type="button" class="pf-switch" role="switch" aria-checked="false" aria-labelledby="pfSw{{ $i }}" data-pf-switch="{{ $s['key'] }}"><span></span></button>
                        </li>
                    @endforeach
                </ul>
                <div class="pf-card__actions pf-card__actions--split">
                    <button type="button" class="dash-btn dash-btn--secondary" data-bs-toggle="modal" data-bs-target="#pfPasswordModal"><x-icon name="lock" :size="18" /> Ubah kata sandi</button>
                    <button type="button" class="dash-btn dash-btn--danger" data-bs-toggle="modal" data-bs-target="#pfLogoutModal"><x-icon name="log-out" :size="18" /> Keluar</button>
                </div>
            </section>
        </div>
    </div>

    {{-- Modal: ubah kata sandi (simulasi; kata sandi tidak disimpan di mana pun) --}}
    <div class="modal fade jl-modal" id="pfPasswordModal" tabindex="-1" aria-labelledby="pfPasswordTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" id="pfPasswordForm" method="POST" action="#" novalidate>
                @csrf
                <div class="modal-header">
                    <h2 class="jl-modal__title" id="pfPasswordTitle">Ubah kata sandi</h2>
                    <button type="button" class="jl-modal__close" data-bs-dismiss="modal" aria-label="Tutup"><x-icon name="x" :size="20" /></button>
                </div>
                <div class="modal-body">
                    @foreach ([['current', 'Kata sandi saat ini', 'current-password'], ['next', 'Kata sandi baru', 'new-password'], ['confirm', 'Ulangi kata sandi baru', 'new-password']] as [$key, $label, $auto])
                        <div class="ad-field">
                            <label for="pfPw_{{ $key }}" class="ad-label">{{ $label }}</label>
                            <div class="password-field">
                                <div class="ad-input"><input type="password" id="pfPw_{{ $key }}" name="password_{{ $key }}" autocomplete="{{ $auto }}"></div>
                                <button type="button" class="password-toggle" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                            </div>
                            @if ($key === 'next')<p class="ad-helper">Minimal 8 karakter, mengandung huruf dan angka.</p>@endif
                            <p class="cz-error" role="alert" data-pf-error="pw_{{ $key }}" hidden><x-icon name="alert-circle" :size="16" /><span></span></p>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="dash-btn dash-btn--secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="dash-btn dash-btn--primary">Simpan kata sandi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: konfirmasi keluar --}}
    <div class="modal fade jl-modal" id="pfLogoutModal" tabindex="-1" aria-labelledby="pfLogoutTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="jl-modal__title" id="pfLogoutTitle">Keluar dari akun?</h2>
                    <button type="button" class="jl-modal__close" data-bs-dismiss="modal" aria-label="Tutup"><x-icon name="x" :size="20" /></button>
                </div>
                <div class="modal-body"><p class="jl-modal__desc">Kamu perlu masuk lagi untuk membuat laporan atau memantau aktivitasmu.</p></div>
                <div class="modal-footer">
                    <button type="button" class="dash-btn dash-btn--secondary" data-bs-dismiss="modal">Tetap di sini</button>
                    <button type="button" class="dash-btn dash-btn--danger-solid" data-pf-logout><x-icon name="log-out" :size="18" /> Keluar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi singkat (diisi JavaScript) --}}
    <div class="pf-toast" data-pf-toast role="status" aria-live="polite" hidden>
        <span class="pf-toast__ico pf-toast__ico--ok"><x-icon name="check" :size="16" /></span>
        <span class="pf-toast__ico pf-toast__ico--err"><x-icon name="alert-circle" :size="16" /></span>
        <span data-pf-toast-text></span>
    </div>

    <script type="application/json" id="profilData">@json(['user' => $user, 'settings' => $settings])</script>
</div>
@endsection