@extends('layouts.dashboard')

@section('title', 'jelajahi')
@section('active', 'jelajahi')

@section('content')
    {{-- JELAJAHI --}}
    <section class="dash-welcome" aria-labelledby="welcomeTitle">
        <h1 class="dash-welcome__title" id="welcomeTitle">Jelajahi Laporan</h1>
        <p class="dash-welcome__meta">
            <span><x-icon name="map-pin" :size="14" /> 1.428 laporan dalam 6 bulan terakhir di Jakarta Pusat</span>
        </p>
    </section>

    {{-- JELAJAHI --}}
    <section>
        
    </section>
@endsection