<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="flex-row-reverse text-center row align-items-center">
            {{-- Bagian Kanan (Dihapus elemen demo seperti Documentation, Source Code, Sponsor) --}}
            <div class="col-lg-auto ms-lg-auto">
                <ul class="mb-0 list-inline list-inline-dots">
                    {{-- Anda bisa menambahkan link relevan di sini jika ada, misalnya kebijakan privasi --}}
                    {{-- <li class="list-inline-item"><a href="#" class="link-secondary">Kebijakan Privasi</a></li> --}}
                </ul>
            </div>
            {{-- Bagian Kiri (Informasi Hak Cipta) --}}
            <div class="mt-3 col-12 col-lg-auto mt-lg-0">
                <ul class="mb-0 list-inline list-inline-dots">
                    <li class="list-inline-item">
                        Copyright &copy; {{ date('Y') }}
                        <a href="{{ route('dashboardadmin') }}" class="link-secondary">Absensi App</a>. {{-- Ganti dengan nama aplikasi Anda --}}
                        All rights reserved.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
