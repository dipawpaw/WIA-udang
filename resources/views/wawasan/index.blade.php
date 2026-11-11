
@extends('layouts.dashboard')

@section('title', 'Wawasan Kota')

@section('active', 'wawasan')

@php
    $wawasan = $wawasan ?? [
        'kota' => 'Jakarta Pusat',
        'periode' => '6 bulan terakhir',
        'rating' => 4.7,
        'positif' => 94,
        'penilai' => 1186,
        'selesai' => 1270,
        'total' => 1428,
        'bulan' => ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
        'masuk' => [190, 215, 232, 248, 261, 282],
        'diselesaikan' => [160, 190, 208, 222, 236, 254],
        'distribusi' => [5 => 940, 4 => 170, 3 => 46, 2 => 20, 1 => 10],
        'kecamatan' => [
            ['Menteng', 4.9],
            ['Gambir', 4.8],
            ['Tanah Abang', 4.7],
            ['Cempaka Putih', 4.7],
            ['Sawah Besar', 4.6],
        ],
        'isu' => [
            ['Jalan rusak', 312],
            ['Lampu jalan padam', 241],
            ['Halte & transportasi', 187],
            ['Sampah menumpuk', 142],
        ],
    ];

    $w = $wawasan;

    $num = fn($n) => number_format($n, 0, ',', '.');
    $dec = fn($n) => number_format($n, 1, ',', '.');

    $pctSelesai = $w['total'] > 0
        ? round($w['selesai'] / $w['total'] * 100)
        : 0;

    $maxIsu = max(array_column($w['isu'], 1));
@endphp

@section('content')

<style>
    .insights-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #29251f;
        padding: 8px 0 32px;
    }

    .insights-page * {
        box-sizing: border-box;
    }

    .insights-page .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 26px;
    }

    .insights-page .eyebrow {
        color: #2563EB;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .insights-page h1 {
        font-size: 27px;
        font-weight: 800;
        margin: 0 0 8px;
        color: #29251f;
    }

    .insights-page .subtitle {
        color: #8a8178;
        font-size: 13px;
        margin: 0;
    }

    .insights-page .period-select {
        border: 1px solid #e9e2d9;
        border-radius: 10px;
        padding: 10px 14px;
        background: #fff;
        color: #51463b;
        font-size: 12px;
        font-weight: 700;
    }

    .insights-page .pulse-card {
        background: #1D4ED8;
        color: white;
        border-radius: 17px;
        padding: 25px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .insights-page .pulse-card::after {
        content: '';
        position: absolute;
        width: 210px;
        height: 210px;
        border: 35px solid rgba(255,255,255,.06);
        border-radius: 50%;
        right: -55px;
        top: -95px;
    }

    .insights-page .pulse-label {
        font-size: 11px;
        letter-spacing: 1.3px;
        font-weight: 800;
        opacity: .7;
        text-transform: uppercase;
    }

    .insights-page .pulse-rating {
        display: flex;
        align-items: center;
        gap: 13px;
        margin: 13px 0;
    }

    .insights-page .pulse-rating strong {
        font-size: 47px;
        line-height: 1;
        font-weight: 800;
    }

    .insights-page .pulse-stars {
        color: #f2bd69;
        font-size: 19px;
        letter-spacing: 2px;
    }

    .insights-page .pulse-description {
        font-size: 12px;
        opacity: .78;
    }

    .insights-page .pulse-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 35px;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid rgba(255,255,255,.17);
    }

    .insights-page .pulse-stat strong {
        display: block;
        font-size: 21px;
        font-weight: 800;
    }

    .insights-page .pulse-stat span {
        font-size: 11px;
        opacity: .7;
    }

    .insights-page .metric-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .insights-page .metric-card,
    .insights-page .panel {
        background: #fff;
        border: 1px solid #eee8e0;
        border-radius: 14px;
        padding: 19px;
    }

    .insights-page .metric-label {
        color: #91877d;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .insights-page .metric-value {
        color: #1F4486;
        font-size: 27px;
        font-weight: 800;
    }

    .insights-page .metric-note {
        color: #9b9188;
        font-size: 10px;
        margin-top: 6px;
    }

    .insights-page .panel-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .insights-page .panel-title {
        font-size: 14px;
        font-weight: 800;
        color: #382b22;
        margin: 0 0 5px;
    }

    .insights-page .panel-subtitle {
        font-size: 11px;
        color: #a1988e;
        margin-bottom: 20px;
    }

    .insights-page .chart-wrap {
        width: 100%;
        overflow: hidden;
    }

    .insights-page .chart-wrap svg {
        width: 100%;
        height: auto;
        display: block;
    }

    .insights-page .legend {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
        margin-top: 12px;
        font-size: 10px;
        color: #81766b;
    }

    .insights-page .legend span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .insights-page .legend i {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
    }

    .insights-page .rating-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 13px 0;
        font-size: 11px;
    }

    .insights-page .rating-number {
        width: 15px;
        color: #1F4486;
        font-weight: 700;
    }

    .insights-page .rating-track,
    .insights-page .issue-track {
        flex: 1;
        height: 8px;
        background: #EFF6FF;
        border-radius: 20px;
        overflow: hidden;
    }

    .insights-page .rating-fill,
    .insights-page .issue-fill {
        height: 100%;
        background: #2563EB;
        border-radius: 20px;
    }

    .insights-page .rating-count {
        width: 35px;
        text-align: right;
        color: #91877d;
    }

    .insights-page .ranking-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #f1eee9;
    }

    .insights-page .ranking-row:last-child {
        border-bottom: 0;
    }

    .insights-page .rank-number {
        width: 27px;
        height: 27px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        background: #DBEAFE;
        color: #1D4ED8;
        font-size: 11px;
        font-weight: 800;
    }

    .insights-page .rank-name {
        flex: 1;
        font-size: 12px;
        font-weight: 700;
    }

    .insights-page .rank-score {
        color: #a56d3e;
        font-size: 12px;
        font-weight: 800;
    }

    .insights-page .issue-row {
        margin: 18px 0;
    }

    .insights-page .issue-heading {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
        font-size: 11px;
    }

    .insights-page .issue-name {
        font-weight: 700;
    }

    .insights-page .issue-count {
        color: #9a8e83;
    }

    .insights-page .issue-fill {
        background: #855038;
    }

    @media (max-width: 850px) {
        .insights-page .panel-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .insights-page .metric-grid {
            grid-template-columns: 1fr;
        }

        .insights-page h1 {
            font-size: 22px;
        }

        .insights-page .pulse-stats {
            gap: 20px;
        }
    }

    .insights-page {
        color: var(--cityzen-text);
        background: transparent;
    }

    .insights-page .eyebrow {
        color: var(--cityzen-primary);
    }

    .insights-page .pulse-card {
        background: linear-gradient(
            100deg,
            var(--cityzen-deep),
            #1F4486 55%,
            var(--cityzen-action)
        );
    }

    .insights-page .pulse-stars {
        color: #FBBF24;
    }

    .insights-page .metric-card,
    .insights-page .panel {
        background: var(--cityzen-white);
        border-color: var(--cityzen-border);
        box-shadow: var(--shadow-1);
    }

    .insights-page .metric-value {
        color: var(--cityzen-primary-dark);
    }

    .insights-page .rating-fill {
        background: var(--cityzen-primary);
    }

    .insights-page .rank-number {
        background: var(--cityzen-chip);
        color: var(--cityzen-primary-dark);
    }

    .insights-page .rank-score {
        color: var(--cityzen-primary);
    }

    .insights-page .issue-fill {
        background: var(--cityzen-action);
    }

    .insights-page .rating-track,
    .insights-page .issue-track {
        background: var(--cityzen-muted);
    }

    .insights-page .panel-title,
    .insights-page h1 {
        color: var(--cityzen-text);
    }

</style>

<div class="insights-page">

    <div class="page-heading">
        <div>
            <div class="eyebrow">Cityzen Insights</div>
            <h1>Wawasan Kota</h1>
            <p class="subtitle">
                Pantau perkembangan dan kualitas pelayanan publik di
                {{ $w['kota'] }}.
            </p>
        </div>

        <select class="period-select" aria-label="Periode laporan">
            <option>{{ $w['periode'] }}</option>
        </select>
    </div>

    {{-- PUBLIC PULSE --}}
    <section class="pulse-card">
        <div class="pulse-label">Public Pulse · {{ $w['kota'] }}</div>

        <div class="pulse-rating">
            <strong>{{ $dec($w['rating']) }}</strong>
            <div>
                <div class="pulse-stars">★★★★★</div>
                <div class="pulse-description">
                    Berdasarkan {{ $num($w['penilai']) }} penilaian warga
                </div>
            </div>
        </div>

        <div class="pulse-description">
            {{ $w['positif'] }}% warga memberikan penilaian positif terhadap
            pelayanan dan penanganan laporan kota.
        </div>

        <div class="pulse-stats">
            <div class="pulse-stat">
                <strong>{{ $num($w['total']) }}</strong>
                <span>Total laporan masuk</span>
            </div>

            <div class="pulse-stat">
                <strong>{{ $num($w['selesai']) }}</strong>
                <span>Laporan diselesaikan</span>
            </div>

            <div class="pulse-stat">
                <strong>{{ $pctSelesai }}%</strong>
                <span>Tingkat penyelesaian</span>
            </div>
        </div>
    </section>

    {{-- METRIC CARDS --}}
    <section class="metric-grid">
        <div class="metric-card">
            <div class="metric-label">RATA-RATA PENILAIAN</div>
            <div class="metric-value">
                {{ $dec($w['rating']) }} <span style="font-size:14px;color:#b77a4b">/ 5</span>
            </div>
            <div class="metric-note">Penilaian keseluruhan warga</div>
        </div>

        <div class="metric-card">
            <div class="metric-label">TOTAL EVALUASI</div>
            <div class="metric-value">{{ $num($w['penilai']) }}</div>
            <div class="metric-note">Warga yang telah memberikan penilaian</div>
        </div>

        <div class="metric-card">
            <div class="metric-label">LAPORAN SELESAI</div>
            <div class="metric-value">{{ $pctSelesai }}%</div>
            <div class="metric-note">
                {{ $num($w['selesai']) }} dari {{ $num($w['total']) }} laporan
            </div>
        </div>
    </section>

    {{-- TREND AND RATING DISTRIBUTION --}}
    <section class="panel-grid">
        <div class="panel">
            <h3 class="panel-title">Tren Laporan Kota</h3>
            <div class="panel-subtitle">
                Perbandingan laporan masuk dan laporan diselesaikan
            </div>

            <div class="chart-wrap">
                <svg viewBox="0 0 800 280" role="img" aria-label="Grafik tren laporan kota">
                    @foreach ([0, 1, 2, 3] as $i)
                        <line
                            x1="55"
                            y1="{{ 30 + $i * 65 }}"
                            x2="770"
                            y2="{{ 30 + $i * 65 }}"
                            stroke="#eee9e2"
                            stroke-dasharray="4 5"
                        />
                    @endforeach

                    <polyline
                        points="{{ implode(' ', array_map(fn($i, $v) => (90 + $i * 136) . ',' . (230 - $v * 200 / 300), array_keys($w['masuk']), $w['masuk'])) }}"
                        fill="none"
                        stroke="#2563EB"
                        stroke-width="4"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <polyline
                        points="{{ implode(' ', array_map(fn($i, $v) => (90 + $i * 136) . ',' . (230 - $v * 200 / 300), array_keys($w['diselesaikan']), $w['diselesaikan'])) }}"
                        fill="none"
                        stroke="#93C5FD"
                        stroke-width="4"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    @foreach ($w['bulan'] as $i => $bulan)
                        <text
                            x="{{ 90 + $i * 136 }}"
                            y="260"
                            text-anchor="middle"
                            fill="#9b9187"
                            font-size="12"
                        >{{ $bulan }}</text>
                    @endforeach
                </svg>
            </div>

            <div class="legend">
                <span><i style="background:#a66b43"></i> Laporan masuk</span>
                <span><i style="background:#d4b58e"></i> Diselesaikan</span>
            </div>
        </div>

        <div class="panel">
            <h3 class="panel-title">Distribusi Penilaian</h3>
            <div class="panel-subtitle">
                Sebaran rating dari warga
            </div>

            @foreach ($w['distribusi'] as $bintang => $jumlah)
                <div class="rating-row">
                    <span class="rating-number">{{ $bintang }} ★</span>

                    <div class="rating-track">
                        <div
                            class="rating-fill"
                            style="width: {{ $w['penilai'] > 0 ? ($jumlah / $w['penilai'] * 100) : 0 }}%"
                        ></div>
                    </div>

                    <span class="rating-count">{{ $num($jumlah) }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- DISTRICT RANKING --}}
    <section class="panel-grid">
        <div class="panel">
            <h3 class="panel-title">Kecamatan dengan Penilaian Tertinggi</h3>
            <div class="panel-subtitle">
                Peringkat berdasarkan rata-rata penilaian warga
            </div>

            @foreach ($w['kecamatan'] as $i => $kecamatan)
                <div class="ranking-row">
                    <div class="rank-number">{{ $i + 1 }}</div>
                    <div class="rank-name">{{ $kecamatan[0] }}</div>
                    <div class="rank-score">
                        ★ {{ $dec($kecamatan[1]) }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="panel">
            <h3 class="panel-title">Isu Paling Banyak Dilaporkan</h3>
            <div class="panel-subtitle">
                Jenis permasalahan yang paling sering disampaikan warga
            </div>

            @foreach ($w['isu'] as $isu)
                <div class="issue-row">
                    <div class="issue-heading">
                        <span class="issue-name">{{ $isu[0] }}</span>
                        <span class="issue-count">{{ $num($isu[1]) }} laporan</span>
                    </div>

                    <div class="issue-track">
                        <div
                            class="issue-fill"
                            style="width: {{ $maxIsu > 0 ? ($isu[1] / $maxIsu * 100) : 0 }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>

@endsection
