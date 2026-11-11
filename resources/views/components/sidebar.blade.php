{{-- Sidebar Cityzen. Pakai: <x-sidebar active="beranda" /> --}}
@props([
    'active' => 'beranda',
    'name'   => 'Siti Aulia',
    'role'   => 'Warga Terverifikasi',
])
@php
    $items = [
        ['key' => 'beranda',  'label' => 'Beranda',       'icon' => 'home',      'href' => url('/homepage')],
        ['key' => 'jelajahi', 'label' => 'Jelajahi',      'icon' => 'compass',   'href' => url('/jelajahi')],
        ['key' => 'aduin',    'label' => 'Aduin',         'icon' => 'megaphone', 'href' => url('/aduin')],
        ['key' => 'aktivitas','label' => 'Aktivitas Saya','icon' => 'clipboard', 'href' => url('/aktivitas')],
        ['key' => 'wawasan',  'label' => 'Wawasan Kota',  'icon' => 'chart',     'href' => url('/wawasan')],
    ];
    $initials = collect(preg_split('/\s+/', trim($name)))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp

<aside class="cz-sidebar" id="czSidebar" aria-label="Navigasi utama">
    <div class="cz-sidebar__brand">
        <a href="{{ url('/homepage') }}" class="cz-sidebar__logo">
            <span class="cz-sidebar__logo-mark"><x-icon name="building" :size="20" /></span>
            <span class="cz-sidebar__logo-text">Cityzen</span>
        </a>
        <button type="button" class="cz-sidebar__close" data-sidebar-toggle aria-label="Tutup menu"><x-icon name="x" :size="20" /></button>
    </div>

    <nav class="cz-sidebar__nav" aria-label="Menu">
        <ul class="cz-sidebar__list">
            @foreach ($items as $item)
                <li>
                    <a href="{{ $item['href'] }}"
                        class="cz-sidebar__link {{ $active === $item['key'] ? 'is-active' : '' }}"
                        @if ($active === $item['key']) aria-current="page" @endif>
                        <x-icon :name="$item['icon']" :size="20" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="cz-sidebar__footer">
        <div class="cz-sidebar__status"><span class="cz-sidebar__status-dot" aria-hidden="true"></span>Sistem siaga 24 jam</div>
        <a href="{{ route('profile.index') }}" class="cz-sidebar__user">
            <span class="cz-sidebar__avatar" aria-hidden="true">
                {{ $initials }}</span>
            <span class="cz-sidebar__user-text">
                <strong>{{ $name }}</strong>
                <small>{{ $role }}</small>
            </span>
        </a>
    </div>
</aside>
<div class="cz-sidebar-backdrop" data-sidebar-toggle></div>