@extends('layouts.dashboard')

@section('title', 'Beranda')
@section('active', 'beranda')

@php
    $onTime = [
        'labels' => ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
        'values' => [76, 82, 85, 89, 92, 94],
    ];
    $status = [
        ['label' => 'Selesai',      'count' => 1270, 'pct' => '88,9', 'color' => '#0B9460'],
        ['label' => 'Dalam proses', 'count' => 124,  'pct' => '8,7',  'color' => '#E3A008'],
        ['label' => 'Menunggu',     'count' => 34,   'pct' => '2,4',  'color' => '#8EA2D7'],
    ];
    $quick = [
        ['title' => 'Buat laporan',   'desc' => 'Foto & kirim',  'icon' => 'camera', 'route' => 'aduin.index'],
        ['title' => 'Lihat laporan',  'desc' => 'Di sekitarmu',  'icon' => 'search', 'route' => 'jelajahi.index'],
        ['title' => 'Beri rating',    'desc' => 'Public Pulse',  'icon' => 'star', 'route' => 'aktivitas.index'],
        ['title' => 'Lihat statistik','desc' => 'Data kota',     'icon' => 'chart', 'route' => 'wawasan.index'],
    ];
    $reports = [
        ['icon' => 'route',     'category' => 'Infrastruktur', 'time' => '2 jam lalu',   'title' => 'Jalan Rusak dan Berlubang di Jl. Sabang',        'location' => 'Menteng, Jakarta Pusat',        'status' => 'Dalam Proses', 'tone' => 'process',  'votes' => 142],
        ['icon' => 'lightbulb', 'category' => 'Penerangan',    'time' => '2 hari lalu',  'title' => 'Lampu Penerangan Jalan Padam Sepanjang 200 m',   'location' => 'Cempaka Putih, Jakarta Pusat',  'status' => 'Selesai',      'tone' => 'done',     'votes' => 88],
        ['icon' => 'bus',       'category' => 'Transportasi',  'time' => '3 hari lalu',  'title' => 'Halte Busway Rusak dan Tidak Terawat',           'location' => 'Gambir, Jakarta Pusat',         'status' => 'Terverifikasi','tone' => 'verified', 'votes' => 204],
    ];
@endphp

@section('content')
    {{-- Welcome --}}
    <section class="dash-welcome" aria-labelledby="welcomeTitle">
        <h1 class="dash-welcome__title" id="welcomeTitle">Selamat datang, Siti</h1>
        <p class="dash-welcome__meta">
            <span><x-icon name="calendar" :size="14" /> Rabu, 15 Oktober 2025</span>
            <span class="dash-welcome__sep" aria-hidden="true">·</span>
            <span><x-icon name="map-pin" :size="14" /> Jakarta Pusat</span>
        </p>
    </section>

    {{-- Hero --}}
    <section class="dash-hero" aria-label="Sorotan">
        <div class="dash-hero__body">
            <h2 class="dash-hero__title">Wujudkan lingkungan yang lebih nyaman, bersih, dan tertata bersama Cityzen</h2>
            <p class="dash-hero__desc">Pantau aduan langsung ke dinas terkait dan nilai mutu pelayanan kota secara real-time.</p>
            <div class="dash-hero__actions">
                <a href="{{ route('aktivitas.index') }}" class="dash-btn dash-btn--light">Lihat Pencapaian <x-icon name="arrow-right" :size="16" /></a>
            </div>
        </div>
        <div class="dash-hero__art" aria-hidden="true">
            <span class="dash-hero__bar"></span><span class="dash-hero__bar"></span><span class="dash-hero__bar"></span>
            <span class="dash-hero__bar dash-hero__bar--pin"><x-icon name="map-pin" :size="26" /></span>
            <span class="dash-hero__bar"></span><span class="dash-hero__bar"></span><span class="dash-hero__bar"></span><span class="dash-hero__bar"></span>
        </div>
    </section>

    {{-- Statistik ringkas --}}
    <section class="dash-stats" aria-label="Ringkasan">
        <article class="dash-card dash-stat">
            <div class="dash-stat__head">
                <h3 class="dash-stat__label">Tiket aktif</h3>
                <span class="dash-icon-box"><x-icon name="ticket" :size="18" /></span>
            </div>
            <p class="dash-stat__value">158 <small>tiket</small></p>
            <div class="dash-stat__badges">
                <span class="dash-badge dash-badge--process"><i></i>124 diproses</span>
                <span class="dash-badge dash-badge--neutral"><i></i>34 menunggu</span>
            </div>
        </article>

        <article class="dash-card dash-stat">
            <div class="dash-stat__head">
                <h3 class="dash-stat__label">Public Pulse</h3>
                <span class="dash-icon-box"><x-icon name="star" :size="18" /></span>
            </div>
            <p class="dash-stat__value">4,7 <small>/ 5,0</small></p>
            <div class="dash-stat__badges">
                <span class="dash-badge dash-badge--done"><i></i>Sentimen positif 94%</span>
            </div>
        </article>

        <article class="dash-card dash-stat">
            <div class="dash-stat__head">
                <h3 class="dash-stat__label">Aktivitas saya</h3>
                <span class="dash-icon-box"><x-icon name="clipboard-check" :size="18" /></span>
            </div>
            <p class="dash-stat__value">5 <small>laporan</small></p>
            <div class="dash-stat__badges">
                <span class="dash-badge dash-badge--done"><i></i>3 selesai</span>
            </div>
        </article>
    </section>

    {{-- Grafik --}}
    <section class="dash-charts" aria-label="Grafik">
        <article class="dash-card dash-chart">
            <h3 class="dash-card__title">Penuntasan tepat waktu</h3>
            <p class="dash-card__desc">Persentase laporan yang selesai sesuai target waktu, per bulan (2025).</p>
            <div class="dash-chart__canvas dash-chart__canvas--bar">
                <canvas id="chartOnTime" role="img" aria-label="Grafik batang penuntasan tepat waktu Mei sampai Oktober 2025"
                        data-labels='@json($onTime['labels'])' data-values='@json($onTime['values'])'></canvas>
            </div>
        </article>

        <article class="dash-card dash-chart">
            <h3 class="dash-card__title">Status laporan</h3>
            <p class="dash-card__desc">6 bulan terakhir</p>
            <div class="dash-chart__canvas dash-chart__canvas--donut">
                <canvas id="chartStatus" role="img" aria-label="Grafik donat status laporan"
                        data-labels='@json(array_column($status, 'label'))'
                        data-values='@json(array_column($status, 'count'))'
                        data-colors='@json(array_column($status, 'color'))'></canvas>
                <div class="dash-chart__center"><strong>1.428</strong><span>laporan</span></div>
            </div>
            <ul class="dash-legend">
                @foreach ($status as $row)
                    <li>
                        <span class="dash-legend__dot" style="background: {{ $row['color'] }}"></span>
                        <span class="dash-legend__label">{{ $row['label'] }}</span>
                        <span class="dash-legend__value"><strong>{{ number_format($row['count'], 0, ',', '.') }}</strong> ({{ $row['pct'] }}%)</span>
                    </li>
                @endforeach
            </ul>
        </article>
    </section>

    {{-- Aktivitas cepat --}}
    <section class="dash-section" aria-labelledby="quickTitle">
        <h2 class="dash-section__title" id="quickTitle">Aktivitas cepat</h2>
        <div class="dash-quick">
            @foreach ($quick as $q)
                <a href="{{ route($q['route']) }}" class="dash-card dash-quick__item">
                    <span class="dash-icon-box dash-icon-box--sm"><x-icon :name="$q['icon']" :size="18" /></span>
                    <span class="dash-quick__text">
                        <strong>{{ $q['title'] }}</strong>
                        <small>{{ $q['desc'] }}</small>
                    </span>
                    <x-icon name="arrow-right" :size="18" class="dash-quick__arrow" />
                </a>
            @endforeach
        </div>
    </section>

    {{-- Laporan terbaru --}}
    <section class="dash-section" aria-labelledby="latestTitle">
        <div class="dash-section__head">
            <h2 class="dash-section__title" id="latestTitle">Laporan terbaru</h2>
            <a href="{{ route('jelajahi.index') }}" class="dash-link">Lihat semua <x-icon name="arrow-right" :size="16" /></a>
        </div>
        <div class="dash-reports">
            @foreach ($reports as $r)
                <article class="dash-card dash-report">
                    <span class="dash-report__icon"><x-icon :name="$r['icon']" :size="26" /></span>
                    <div class="dash-report__main">
                        <div class="dash-report__meta">
                            <span class="dash-tag">{{ $r['category'] }}</span>
                            <span class="dash-report__time">{{ $r['time'] }}</span>
                        </div>
                        <h3 class="dash-report__title">{{ $r['title'] }}</h3>
                        <p class="dash-report__loc"><x-icon name="map-pin" :size="14" /> {{ $r['location'] }}</p>
                    </div>
                    <div class="dash-report__side">
                        <span class="dash-badge dash-badge--{{ $r['tone'] }}"><i></i>{{ $r['status'] }}</span>
                        <span class="dash-report__votes"><x-icon name="thumbs-up" :size="14" /> {{ $r['votes'] }} dukungan</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection