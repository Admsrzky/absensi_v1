<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('dashboardadmin') }}"> {{-- Mengarahkan ke dashboard admin --}}
                {{-- Menggunakan logo dari assets/img/blm.jpg atau logo Tabler jika blm.jpg tidak cocok --}}
                <img src="{{asset('assets/img/blm.jpg')}}" width="110" height="32" alt="Logo Aplikasi" class="navbar-brand-image">
                {{-- Alternatif jika logo.svg Tabler lebih disukai: --}}
                {{-- <img src="{{asset('tabler/static/logo.svg')}}" width="110" height="32" alt="Tabler" class="navbar-brand-image"> --}}
            </a>
        </h1>
        <div class="flex-row navbar-nav d-lg-none">
            {{-- Dark/Light Mode Toggle --}}
            <div class="d-none d-lg-flex">
                <a href="?theme=dark" class="px-0 nav-link hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                </a>
                <a href="?theme=light" class="px-0 nav-link hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                </a>
            </div>
            {{-- Notifications Dropdown --}}
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="px-0 nav-link" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                    </svg>
                    {{-- Badge for pending izin/sakit --}}
                    @if(isset($jumlahIzin) && $jumlahIzin > 0)
                    <span class="badge bg-red">{{ $jumlahIzin }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notifikasi</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable">
                            {{-- Contoh Notifikasi --}}
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto"><span class="status-dot status-dot-animated bg-red d-block"></span></div>
                                    <div class="col text-truncate">
                                        <a href="{{ route('absen.izinsakit') }}" class="text-body d-block">Pengajuan Izin/Sakit</a>
                                        <div class="d-block text-secondary text-truncate mt-n1">
                                            Ada {{ $jumlahIzin ?? 0 }} pengajuan izin/sakit yang menunggu persetujuan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Anda bisa menambahkan notifikasi lain di sini --}}
                        </div>
                    </div>
                </div>
            </div>
            {{-- User Profile Dropdown --}}
            <div class="nav-item dropdown">
                <a href="#" class="p-0 nav-link d-flex lh-1 text-reset" data-bs-toggle="dropdown" aria-label="Open user menu">
                    @php
                        $userPhotoPath = Auth::check() && !empty(Auth::user()->foto)
                            ? Storage::url('uploads/karyawan/' . Auth::user()->foto)
                            : asset('assets/img/blm.jpg');
                    @endphp
                    <span class="avatar avatar-sm" style="background-image: url({{ $userPhotoPath }})"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::check() ? Auth::user()->nama : 'Guest' }}</div>
                        <div class="mt-1 small text-secondary">{{ Auth::check() ? Auth::user()->jabatan : 'N/A' }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('editprofile') }}" class="dropdown-item">Profile</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                {{-- Menu Home --}}
                <li class="nav-item {{ request()->routeIs('dashboardadmin') ? 'active' : '' }}">
                    <a class="nav-link" href="{{route('dashboardadmin')}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Home
                        </span>
                    </a>
                </li>

                {{-- Menu Data Master --}}
                <li class="nav-item dropdown {{ request()->is(['user', 'absen', 'user/*', 'absen/*']) ? 'active show' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->is(['user', 'absen', 'user/*', 'absen/*']) ? 'true' : '' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12l0 9" />
                                <path d="M12 12l-8 -4.5" />
                                <path d="M16 5.25l-8 4.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Data Master
                        </span>
                    </a>
                    <div class="dropdown-menu {{ request()->is(['user', 'absen', 'user/*', 'absen/*']) ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                {{-- Submenu: Data Karyawan --}}
                                <a class="dropdown-item {{ request()->routeIs('user.index') || request()->is('user/*') ? 'active' : '' }}" href="{{route('user.index')}}">
                                    Data Karyawan
                                </a>
                                {{-- <a class="dropdown-item {{ request()->routeIs('admin.jabatan.index') ? 'active' : '' }}" href="{{route('admin.jabatan.index')}}">
                                    Data Jabatan
                                </a> --}}
                                {{-- Submenu: Data Absen --}}
                                <a class="dropdown-item {{ request()->routeIs('absen.index') || (request()->is('absen/*') && !request()->routeIs('absen.monitor') && !request()->routeIs('absen.izinsakit') && !request()->routeIs('absen.laporan') && !request()->routeIs('absen.rekap')) ? 'active' : '' }}" href="{{route('absen.index')}}">
                                    Data Absen
                                </a>

                                {{-- Link baru untuk Pengaturan Jam Absensi --}}
                                <a class="dropdown-item {{ request()->routeIs('admin.settings.absentime') ? 'active' : '' }}" href="{{ route('admin.settings.absentime') }}">
                                    Pengaturan Jam Absensi
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                {{-- Menu Monitoring Absen --}}
                <li class="nav-item {{ request()->routeIs('absen.monitor') ? 'active' : '' }}">
                    <a class="nav-link" href="{{route('absen.monitor')}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-desktop" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10z" />
                                <path d="M7 20h10" />
                                <path d="M9 16v4" />
                                <path d="M15 16v4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Monitoring Absen
                        </span>
                    </a>
                </li>

                {{-- Menu Pengajuan Izin / Sakit --}}
                <li class="nav-item {{ request()->routeIs('absen.izinsakit') ? 'active' : '' }}">
                    <a class="nav-link" href="{{route('absen.izinsakit')}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart-rate-monitor" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 4m0 1a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z" />
                                <path d="M7 20h10" />
                                <path d="M9 16v4" />
                                <path d="M15 16v4" />
                                <path d="M7 10h2l2 3l2 -6l1 3h3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Pengajuan Izin / Sakit
                            @if(isset($jumlahIzin) && $jumlahIzin > 0)
                            <small class="mt-1 text-white badge badge-danger bg-red d-block">{{ $jumlahIzin }}</small>
                            @endif
                        </span>
                    </a>
                </li>

                {{-- Menu Laporan --}}
                <li class="nav-item dropdown {{ request()->is(['absen/laporan', 'absen/rekap']) ? 'active show' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#navbar-laporan" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->is(['absen/laporan', 'absen/rekap']) ? 'true' : '' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-description" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M9 17h6" />
                                <path d="M9 13h6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Laporan
                        </span>
                    </a>
                    <div class="dropdown-menu {{ request()->is(['absen/laporan', 'absen/rekap']) ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                {{-- <a class="dropdown-item {{ request()->routeIs('absen.laporan') ? 'active' : '' }}" href="{{route('absen.laporan')}}">
                                    Absensi
                                </a> --}}
                                <a class="dropdown-item {{ request()->routeIs('absen.rekap') ? 'active' : '' }}" href="{{route('absen.rekap')}}">
                                    Rekap Absensi
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                {{-- Menu Halaman User (Fixed at bottom) --}}
                <li class="mt-auto nav-item">
                    <a class="nav-link" href="{{route('absen.dashboard')}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Halaman User
                        </span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</aside>
