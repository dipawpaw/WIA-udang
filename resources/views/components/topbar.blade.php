{{-- Topbar Cityzen. Pakai: <x-topbar initials="SA" /> --}}
@props([
    'initials'  => 'SA',
    'hasNotif'  => true,
])
<header class="cz-topbar">
    <button type="button" class="cz-topbar__menu" data-sidebar-toggle aria-controls="czSidebar" aria-label="Buka menu">
        <x-icon name="menu" :size="22" />
    </button>

    <form class="cz-search" role="search" action="#" method="GET">
        <x-icon name="search" :size="18" />
        <input type="search" name="q" class="cz-search__input" placeholder="Cari laporan, nomor tiket, atau lokasi" aria-label="Cari laporan, nomor tiket, atau lokasi">
    </form>

    <div class="cz-topbar__actions">
        <button type="button" class="cz-icon-btn" aria-label="Notifikasi">
            <x-icon name="bell" :size="20" />
            @if ($hasNotif)<span class="cz-icon-btn__dot" aria-hidden="true"></span>@endif
        </button>
        <a href="#" class="cz-topbar__cta">
            <x-icon name="plus" :size="18" />
            <span>Buat Laporan</span>
        </a>
        <span class="cz-topbar__avatar" title="Profil">{{ $initials }}</span>
    </div>
</header>