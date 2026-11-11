@extends('layouts.dashboard')

@section('title', 'Jelajahi Laporan')

@section('active', 'jelajahi')

{{-- DUMMY DATA --}}
@php
    $region = 'Jakarta Pusat';

    $categories = [
        'infrastruktur' => ['label' => 'Infrastruktur', 'icon' => 'road'],
        'penerangan'    => ['label' => 'Penerangan', 'icon' => 'lightbulb'],
        'transportasi'  => ['label' => 'Transportasi', 'icon' => 'bus'],
        'kebersihan'    => ['label' => 'Kebersihan', 'icon' => 'trash'],
        'drainase'      => ['label' => 'Air & Drainase', 'icon' => 'droplets'],
    ];

    $statuses = [
        'terverifikasi' => ['label' => 'Terverifikasi', 'class' => 'verified'],
        'proses'        => ['label' => 'Dalam Proses', 'class' => 'process'],
        'selesai'       => ['label' => 'Selesai', 'class' => 'done'],
    ];

    $reports = [
        
    ];

    $reportsJs = collect($reports)->map(fn ($r) => $r + [
        'category_label' => $categories[$r['category']]['label'],
        'status_label'   => $statuses[$r['status']]['label'],
    ])->values();
@endphp

@section('content')
    <div id="jelajahi" class="jl">
        {{-- Heading --}}
        <header class="jl-head">
            <div>
                <h1 class="jl-head__title">Jelajahi Laporan</h1>
                <p class="jl-head__meta">
                    <span class="jl-head__place">
                        <x-icon name="map-pin" :size="16" />
                        Laporan publik di sekitar {{ $region ?? 'Jakarta Pusat' }}
                    </span>
                    <strong data-jl-count aria-live="polite">Menampilkan {{ count($reports ?? []) }} dari {{ count($reports ?? []) }} laporan</strong>
                </p>
            </div>
            <span class="dash-badge dash-badge--neutral">Data dan peta contoh</span>
        </header>

        {{-- Filter --}}
        <div class="jl-filters">
            <div class="jl-chips" role="group" aria-label="Filter kategori">
                <button type="button" class="jl-chip is-active" data-jl-chip="semua" aria-pressed="true">Semua</button>
                @foreach ($categories as $key => $cat)
                    <button type="button" class="jl-chip" data-jl-chip="{{ $key }}" aria-pressed="false">{{ $cat['label'] }}</button>
                @endforeach
            </div>

            <div class="jl-selects">
                <div class="dropdown">
                    <button type="button" class="jl-select" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Filter status">
                        <x-icon name="filter" :size="16" />
                        <span data-jl-toggle-label="status">Status</span>
                        <x-icon name="chevron-down" :size="16" />
                    </button>
                    <div class="dropdown-menu dropdown-menu-end jl-menu" role="menu">
                        <button type="button" class="dropdown-item is-active" role="menuitemradio" aria-checked="true" data-jl-option="status" data-value="semua" data-label="Status">
                            Semua status <x-icon name="check" :size="16" />
                        </button>
                        @foreach ($statuses as $key => $st)
                            <button type="button" class="dropdown-item" role="menuitemradio" aria-checked="false" data-jl-option="status" data-value="{{ $key }}" data-label="{{ $st['label'] }}">
                                {{ $st['label'] }} <x-icon name="check" :size="16" />
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="dropdown">
                    <button type="button" class="jl-select" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Urutkan laporan">
                        <x-icon name="clock" :size="16" />
                        <span data-jl-toggle-label="sort">Terbaru</span>
                        <x-icon name="chevron-down" :size="16" />
                    </button>
                    <div class="dropdown-menu dropdown-menu-end jl-menu" role="menu">
                        <button type="button" class="dropdown-item is-active" role="menuitemradio" aria-checked="true" data-jl-option="sort" data-value="terbaru" data-label="Terbaru">
                            Terbaru <x-icon name="check" :size="16" />
                        </button>
                        <button type="button" class="dropdown-item" role="menuitemradio" aria-checked="false" data-jl-option="sort" data-value="populer" data-label="Terpopuler">
                            Terpopuler <x-icon name="check" :size="16" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar (kiri) + peta (kanan). Mobile: satu kolom. --}}
        <div class="jl-layout">

            <section aria-label="Daftar laporan">
                <div class="jl-list" data-jl-list>
                    @foreach ($reports as $r)
                        @php $st = $statuses[$r['status']]; @endphp
                        <article class="dash-card jl-card" data-id="{{ $r['id'] }}">
                            <span class="jl-card__icon"><x-icon :name="$categories[$r['category']]['icon']" :size="28" /></span>

                            <div class="jl-card__body">
                                <div class="jl-card__meta">
                                    <span class="dash-tag">{{ $categories[$r['category']]['label'] }}</span>
                                    <span class="dash-report__time">{{ $r['time'] }}</span>
                                </div>
                                <h2 class="jl-card__title">
                                    <button type="button" class="jl-card__title-btn" aria-pressed="false">{{ $r['title'] }}</button>
                                </h2>
                                <p class="jl-card__loc"><x-icon name="map-pin" :size="14" /> {{ $r['area'] }}</p>
                                <button type="button" class="jl-card__more" data-jl-open>Lihat detail <x-icon name="arrow-right" :size="14" /></button>
                            </div>

                            <div class="jl-card__side">
                                <span class="dash-badge dash-badge--{{ $st['class'] }}"><i></i>{{ $st['label'] }}</span>
                                <span class="jl-card__votes"><x-icon name="thumbs-up" :size="16" /> <span data-jl-votes>{{ $r['votes'] }}</span></span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="dash-card jl-empty" data-jl-empty hidden>
                    <span class="dash-icon-box"><x-icon name="search-x" :size="20" /></span>
                    <h2 class="jl-empty__title">Tidak ada laporan yang cocok</h2>
                    <p class="jl-empty__text">Coba kata kunci lain, atau hapus filter kategori dan status.</p>
                    <button type="button" class="dash-btn dash-btn--secondary" data-jl-reset>Atur ulang filter</button>
                </div>
            </section>

            <section class="jl-map" aria-label="Peta laporan (ilustrasi)">
                <div class="jl-map__world" data-jl-world>
                    <svg class="jl-map__svg" viewBox="0 0 600 512" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
                        <circle class="jl-map__park" cx="182" cy="176" r="92" />
                        <circle class="jl-map__park" cx="548" cy="500" r="96" />
                        <g class="jl-map__roads">
                            <path d="M-40 40 L520 600" />
                            <path d="M140 -40 L700 520" />
                            <path d="M-40 330 L240 610" />
                            <path d="M-20 560 L580 -40" />
                            <path d="M200 610 L660 150" />
                            <path d="M-40 250 L250 -40" />
                        </g>
                    </svg>

                    @foreach ($reports as $r)
                        <button type="button" class="jl-marker" data-id="{{ $r['id'] }}" data-status="{{ $r['status'] }}"
                            style="left: {{ $r['x'] }}%; top: {{ $r['y'] }}%;"
                            aria-label="{{ $r['title'] }}, {{ $statuses[$r['status']]['label'] }}">
                            <svg viewBox="0 0 30 38" aria-hidden="true" focusable="false">
                                <path d="M15 0C6.7 0 0 6.6 0 14.8 0 25.6 15 38 15 38s15-12.4 15-23.2C30 6.6 23.3 0 15 0z" />
                                <circle cx="15" cy="14.5" r="5.5" fill="#fff" />
                            </svg>
                        </button>
                    @endforeach
                </div>

                <ul class="jl-legend" aria-label="Keterangan warna marker">
                    @foreach ($statuses as $key => $st)
                        <li><span class="jl-legend__dot" data-status="{{ $key }}"></span>{{ $st['label'] }}</li>
                    @endforeach
                </ul>

                <div class="jl-zoom">
                    <button type="button" class="jl-zoom__btn" data-jl-zoom="in" aria-label="Perbesar peta"><x-icon name="plus" :size="20" /></button>
                    <button type="button" class="jl-zoom__btn" data-jl-zoom="out" aria-label="Perkecil peta" disabled><x-icon name="minus" :size="20" /></button>
                </div>

                {{-- Ringkasan laporan terpilih (diisi JavaScript dari kartu yang aktif) --}}
                <div class="jl-info" data-jl-info aria-live="polite">
                    <span class="jl-info__icon" data-jl-info-icon></span>
                    <div class="jl-info__body">
                        <div class="jl-info__meta">
                            <span data-jl-info-badge></span>
                            <span class="dash-report__time" data-jl-info-time></span>
                        </div>
                        <h2 class="jl-info__title" data-jl-info-title></h2>
                        <p class="jl-info__sub"><span data-jl-info-place></span> <span data-jl-info-votes></span></p>
                    </div>
                    <button type="button" class="dash-btn dash-btn--primary" data-jl-open>Lihat Detail <x-icon name="arrow-right" :size="18" /></button>
                </div>
            </section>
        </div>

        {{-- Modal detail laporan --}}
        <div class="modal fade jl-modal" id="jelajahiModal" tabindex="-1" aria-labelledby="jelajahiModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="jl-modal__head">
                            <span class="jl-card__icon" data-jl-modal-icon></span>
                            <div>
                                <div class="jl-card__meta">
                                    <span class="dash-tag" data-jl-modal-category></span>
                                    <span data-jl-modal-badge></span>
                                </div>
                                <h2 class="jl-modal__title" id="jelajahiModalTitle" data-jl-modal-title></h2>
                            </div>
                        </div>
                        <button type="button" class="jl-modal__close" data-bs-dismiss="modal" aria-label="Tutup"><x-icon name="x" :size="20" /></button>
                    </div>
                    <div class="modal-body">
                        <ul class="jl-facts">
                            <li><x-icon name="ticket" :size="18" /><span><small>Nomor tiket</small><strong data-jl-modal-ticket></strong></span></li>
                            <li><x-icon name="map-pin" :size="18" /><span><small>Lokasi</small><strong data-jl-modal-place></strong></span></li>
                            <li><x-icon name="clock" :size="18" /><span><small>Dilaporkan</small><strong data-jl-modal-time></strong></span></li>
                            <li><x-icon name="thumbs-up" :size="18" /><span><small>Dukungan</small><strong data-jl-modal-votes></strong></span></li>
                        </ul>
                        <h3 class="jl-modal__subtitle">Deskripsi</h3>
                        <p class="jl-modal__desc" data-jl-modal-desc></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="dash-btn dash-btn--secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="dash-btn dash-btn--primary" data-jl-support aria-pressed="false">
                            <x-icon name="thumbs-up" :size="18" /><span data-jl-support-label>Dukung laporan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script type="application/json" id="jelajahiData">@json($reportsJs)</script>
    </div>
@endsection