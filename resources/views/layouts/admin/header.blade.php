<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
    <div class="container-xl">
        {{-- Tombol Toggler Navbar (untuk mobile, meskipun header ini d-none d-lg-flex) --}}
        {{-- Ini biasanya ada di header utama yang terlihat di mobile, jika ada --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="flex-row navbar-nav order-md-last">
            {{-- Dark/Light Mode Toggle (Dihapus dari sini karena sudah ada di Sidebar) --}}
            {{-- Notifikasi (Dihapus dari sini karena sudah ada di Sidebar) --}}

            {{-- User Profile Dropdown (untuk tampilan desktop) --}}
            <div class="nav-item dropdown">
                <a href="#" class="p-0 nav-link d-flex lh-1 text-reset" data-bs-toggle="dropdown" aria-label="Open user menu">
                    @php
                        // Pastikan Auth::user() tersedia sebelum mencoba mengakses propertinya
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
                    {{-- <a href="./settings.html" class="dropdown-item">Settings</a> --}} {{-- Dihapus jika tidak digunakan --}}
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logoutadmin') }}" method="post">
                        @csrf
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>

        {{-- Search Form (Dihapus jika tidak ada fungsionalitas backend) --}}
        {{-- Jika ingin mempertahankan search, pastikan ada backend yang mengolahnya --}}
        {{-- <div class="collapse navbar-collapse" id="navbar-menu">
            <div>
                <form action="./" method="get" autocomplete="off" novalidate>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg>
                        </span>
                        <input type="text" value="" class="form-control" placeholder="Search…" aria-label="Search in website">
                    </div>
                </form>
            </div>
        </div> --}}

    </div>
</header>
