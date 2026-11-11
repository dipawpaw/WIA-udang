@extends('layouts.dashboard')

@section('title', 'Aktivitas Saya')
@section('active', 'aktivitas')

@php
    /*
     |--------------------------------------------------------------------------
     | DATA CONTOH (dummy) — bukan data sungguhan.
     | Ganti $reports dengan hasil query laporan milik pengguna yang login.
     | Satu sumber data: ringkasan, daftar, detail, timeline, bukti, dan Riwayat Pulse
     | semuanya dihitung dari array ini (dan dikirim ke JavaScript lewat #aktivitasData).
     |
     | status : terkirim | terverifikasi | proses | bukti | selesai | ditolak
     | dates  : waktu tiap tahap [Terkirim, Terverifikasi, Tindak lanjut, Selesai]; null = belum ada
     | evidence: null, atau ['photo' => url|null, 'caption' => ..., 'officer' => ...]
     | pulse  : null, atau ['verdict' => 'sudah'|'belum', 'rating' => 1-5, 'date' => ..., 'comment' => ...]
     | note   : keterangan tambahan (mis. alasan ditolak), boleh null
     */
    $reports = [
        [
            'id' => 1, 'ticket' => 'CZ-20251015-0512', 'category' => 'infrastruktur', 'category_label' => 'Infrastruktur', 'icon' => 'road',
            'title' => 'Jalan Rusak dan Berlubang di Jl. Sabang', 'area' => 'Menteng', 'city' => 'Jakarta Pusat',
            'date' => '15 Okt', 'status' => 'proses',
            'dates' => ['15 Okt, 08.20', '15 Okt, 13.05', '16 Okt, 09.00', null],
            'evidence' => null, 'pulse' => null, 'note' => null,
        ],
        [
            'id' => 2, 'ticket' => 'CZ-20251013-0467', 'category' => 'penerangan', 'category_label' => 'Penerangan', 'icon' => 'lightbulb',
            'title' => 'Lampu Penerangan Jalan Padam Sepanjang 200 m', 'area' => 'Cempaka Putih', 'city' => 'Jakarta Pusat',
            'date' => '13 Okt', 'status' => 'selesai',
            'dates' => ['13 Okt, 09.10', '13 Okt, 14.30', '14 Okt, 08.00', '15 Okt, 07.45'],
            'evidence' => ['photo' => null, 'caption' => 'Foto dari petugas Dinas Bina Marga.', 'officer' => 'Dinas Bina Marga'],
            'pulse' => null, 'note' => null,
        ],
        [
            'id' => 3, 'ticket' => 'CZ-20251012-0391', 'category' => 'transportasi', 'category_label' => 'Transportasi', 'icon' => 'bus',
            'title' => 'Halte Busway Rusak dan Tidak Terawat', 'area' => 'Gambir', 'city' => 'Jakarta Pusat',
            'date' => '12 Okt', 'status' => 'terverifikasi',
            'dates' => ['12 Okt, 10.40', '12 Okt, 15.15', null, null],
            'evidence' => null, 'pulse' => null, 'note' => null,
        ],
        [
            'id' => 4, 'ticket' => 'CZ-20251008-0220', 'category' => 'infrastruktur', 'category_label' => 'Infrastruktur', 'icon' => 'road',
            'title' => 'Trotoar Retak di Jl. Kebon Sirih', 'area' => 'Menteng', 'city' => 'Jakarta Pusat',
            'date' => '8 Okt', 'status' => 'selesai',
            'dates' => ['8 Okt, 07.30', '8 Okt, 11.00', '9 Okt, 08.30', '10 Okt, 16.10'],
            'evidence' => ['photo' => null, 'caption' => 'Foto dari petugas Dinas Bina Marga.', 'officer' => 'Dinas Bina Marga'],
            'pulse' => ['verdict' => 'sudah', 'rating' => 5, 'date' => '10 Okt, 18.20', 'comment' => 'Trotoar sudah rata dan aman dilewati.'],
            'note' => null,
        ],
        [
            'id' => 5, 'ticket' => 'CZ-20251003-0104', 'category' => 'air', 'category_label' => 'Air & Drainase', 'icon' => 'droplets',
            'title' => 'Saluran Air Tersumbat di Jl. Cikini', 'area' => 'Menteng', 'city' => 'Jakarta Pusat',
            'date' => '3 Okt', 'status' => 'selesai',
            'dates' => ['3 Okt, 06.50', '3 Okt, 10.20', '4 Okt, 09.15', '5 Okt, 15.40'],
            'evidence' => ['photo' => null, 'caption' => 'Foto dari petugas Dinas Sumber Daya Air.', 'officer' => 'Dinas Sumber Daya Air'],
            'pulse' => ['verdict' => 'belum', 'rating' => 2, 'date' => '6 Okt, 19.05', 'comment' => 'Air masih menggenang saat hujan.'],
            'note' => null,
        ],
    ];

    $statusMeta = [
        'terkirim'      => ['label' => 'Terkirim',         'tone' => 'verified'],
        'terverifikasi' => ['label' => 'Terverifikasi',    'tone' => 'verified'],
        'proses'        => ['label' => 'Dalam Proses',     'tone' => 'process'],
        'bukti'         => ['label' => 'Memerlukan Bukti', 'tone' => 'rejected'],
        'selesai'       => ['label' => 'Selesai',          'tone' => 'done'],
        'ditolak'       => ['label' => 'Ditolak',          'tone' => 'rejected'],
    ];
    $stages = ['Terkirim', 'Terverifikasi petugas', 'Tindak lanjut lapangan', 'Selesai'];

    // Angka ringkasan dihitung dari $reports, tidak ditulis manual.
    $all = collect($reports);
    $countBy = fn (array $keys) => $all->whereIn('status', $keys)->count();
    $stats = [
        ['label' => 'Total laporan',  'value' => $all->count(),                   'icon' => 'clipboard',    'tone' => ''],
        ['label' => 'Selesai',        'value' => $countBy(['selesai']),           'icon' => 'circle-check', 'tone' => 'done'],
        ['label' => 'Dalam proses',   'value' => $countBy(['proses', 'bukti']),   'icon' => 'clock',        'tone' => 'process'],
        ['label' => 'Terverifikasi',  'value' => $countBy(['terverifikasi']),     'icon' => 'shield-check', 'tone' => 'verified'],
    ];

    // Chip filter hanya untuk status yang benar-benar ada di data.
    $chipOrder = ['proses', 'bukti', 'terkirim', 'terverifikasi', 'selesai', 'ditolak'];
    $chips = collect($chipOrder)->filter(fn ($s) => $all->contains('status', $s))->values();
    $pulseCount = $all->filter(fn ($r) => ! empty($r['pulse']))->count();
@endphp

@section('content')
<div class="ac ac-page" id="aktivitas">
    <header class="ac-header">
        <h1 class="ac-header__title">Aktivitas Saya</h1>
        <p class="ac-header__meta">Pantau laporanmu sampai selesai</p>
    </header>

    <section class="ac-stats" aria-label="Ringkasan laporan">
        @foreach ($stats as $s)
            <div class="dash-card ac-stat-card">
                <span class="ac-stat-card__icon {{ $s['tone'] ? 'ac-stat-card__icon--' . $s['tone'] : '' }}"><x-icon :name="$s['icon']" :size="22" /></span>
                <span class="ac-stat-card__text">
                    <strong>{{ $s['value'] }}</strong>
                    <small>{{ $s['label'] }}</small>
                </span>
            </div>
        @endforeach
    </section>

    @if ($all->isEmpty())
        <section class="dash-card ac-empty" aria-labelledby="acEmptyTitle">
            <span class="ac-empty__art"><x-icon name="clipboard" :size="40" /></span>
            <h2 class="ac-empty__title" id="acEmptyTitle">Belum ada laporan</h2>
            <p class="ac-empty__text">Laporan yang kamu buat akan muncul di sini, lengkap dengan perkembangan penanganannya.</p>
            <a href="{{ route('aduin.index') }}" class="dash-btn dash-btn--primary"><x-icon name="plus" :size="18" />Buat laporan pertama</a>
        </section>
    @else
        <div class="ac-tabs" role="tablist" aria-label="Aktivitas saya">
            <button type="button" class="ac-tab" role="tab" id="acTabReports" aria-controls="acPanelReports" aria-selected="true" data-ac-tab="reports">Laporan Saya</button>
            <button type="button" class="ac-tab" role="tab" id="acTabPulse" aria-controls="acPanelPulse" aria-selected="false" tabindex="-1" data-ac-tab="pulse">Riwayat Pulse <span class="ac-count" data-ac-pulse-count>{{ $pulseCount }}</span></button>
        </div>

        {{-- Tab 1: Laporan Saya --}}
        <div id="acPanelReports" role="tabpanel" aria-labelledby="acTabReports" data-ac-panel="reports">
            <div class="ac-content">
                <div class="ac-side">
                    <div class="ac-filter">
                        <div class="jl-chips" role="group" aria-label="Filter status laporan">
                            <button type="button" class="jl-chip is-active" data-ac-chip="semua" aria-pressed="true">Semua <span class="ac-count">{{ $all->count() }}</span></button>
                            @foreach ($chips as $key)
                                <button type="button" class="jl-chip" data-ac-chip="{{ $key }}" aria-pressed="false">{{ $statusMeta[$key]['label'] }} <span class="ac-count">{{ $all->where('status', $key)->count() }}</span></button>
                            @endforeach
                        </div>
                        <p class="ac-filter__count" data-ac-count aria-live="polite">Menampilkan {{ $all->count() }} dari {{ $all->count() }} laporan</p>
                    </div>

                    <ul class="ac-list" data-ac-list>
                        @foreach ($reports as $r)
                            @php $m = $statusMeta[$r['status']]; @endphp
                            <li data-ac-item data-id="{{ $r['id'] }}">
                                <button type="button" class="ac-card" data-ac-card aria-pressed="false">
                                    <span class="ac-card__icon"><x-icon :name="$r['icon']" :size="24" /></span>
                                    <span class="ac-card__main">
                                        <span class="ac-card__meta"><span class="ac-cat">{{ $r['category_label'] }}</span><span>{{ $r['date'] }}</span></span>
                                        <span class="ac-card__title">{{ $r['title'] }}</span>
                                        <span class="ac-card__place">{{ $r['area'] }}</span>
                                    </span>
                                    <span class="dash-badge dash-badge--{{ $m['tone'] }}">{{ $m['label'] }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="dash-card ac-empty ac-empty--small" data-ac-empty hidden>
                        <span class="ac-empty__art"><x-icon name="search-x" :size="28" /></span>
                        <h2 class="ac-empty__title">Tidak ada laporan yang cocok</h2>
                        <p class="ac-empty__text" data-ac-empty-text>Coba ganti filter atau kata kunci pencarian.</p>
                        <button type="button" class="dash-btn dash-btn--secondary" data-ac-reset>Atur ulang filter</button>
                    </div>

                    <p class="ac-sample-note">Data contoh untuk tampilan. Belum terhubung ke akun atau server.</p>
                </div>

                <section class="dash-card ac-detail" data-ac-detail aria-label="Detail laporan"></section>
            </div>
        </div>

        {{-- Tab 2: Riwayat Pulse --}}
        <div id="acPanelPulse" role="tabpanel" aria-labelledby="acTabPulse" data-ac-panel="pulse" hidden>
            <p class="ac-pulse__intro">Riwayat konfirmasi dan penilaianmu terhadap penyelesaian laporan.</p>
            <ul class="ac-pulse-list" data-ac-pulse-list></ul>
            <div class="dash-card ac-empty" data-ac-pulse-empty hidden>
                <span class="ac-empty__art"><x-icon name="star" :size="40" /></span>
                <h2 class="ac-empty__title">Belum ada riwayat Pulse</h2>
                <p class="ac-empty__text" data-ac-pulse-empty-text>Setelah laporanmu selesai, kamu bisa mengonfirmasi dan memberi rating. Riwayatnya muncul di sini.</p>
            </div>
        </div>

        <script type="application/json" id="aktivitasData">@json(['stages' => $stages, 'status' => $statusMeta, 'reports' => $reports])</script>
    @endif
</div>
@endsection