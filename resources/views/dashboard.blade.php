@extends('layouts.app')

@section('content')

<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ __('Dashboard') }}
    </h2>
</x-slot>

<div id="loader" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-80 z-[9999] transition-opacity duration-300 hidden">
    <div class="w-12 h-12 border-4 border-green-500 rounded-full spinner-border border-r-transparent animate-spin" role="status"></div>
</div>
<div id="appCapsule" class="px-4 pt-4 pb-1">
    <div class="mb-4 section">
        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            Selamat Datang, {{ auth()->user()->nama }}!
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Semoga harimu menyenangkan.
        </p>
    </div>

    <div class="mb-4 section" id="user-section">
        <div id="user-detail" class="flex items-center p-4 space-x-4 bg-white rounded-lg shadow-md">
            <div class="flex-shrink-0 avatar">
                @if(!empty(auth()->user()->foto))
                @php
                $path = Storage::url('uploads/karyawan/'. auth()->user()->foto);
                @endphp
                <img src="{{ $path }}" alt="avatar" class="object-cover w-24 h-24 bg-white border-2 border-gray-200 rounded-full" />
                @else
                @php
                $path = asset('assets/img/blm.jpg');
                @endphp
                <img src="{{ $path }}" alt="avatar" class="object-cover w-24 h-24 bg-white border-2 border-gray-200 rounded-full" />
                @endif
            </div>
            <div id="user-info">
                <h2 id="user-name" class="text-xl font-semibold text-gray-800">{{ auth()->user()->nama}}</h2>
                <span id="user-role" class="text-sm text-gray-600">{{ auth()->user()->jabatan }}</span>
            </div>
        </div>
    </div>

    <div class="mb-4 section">
        <div class="bg-white rounded-lg shadow-md card">
            <div class="p-4 text-center card-body">
                <div class="flex justify-around py-2 list-menu">
                    <div class="flex flex-col items-center text-center item-menu">
                        <div class="menu-icon">
                            <a href="{{route('editprofile')}}" class="flex items-center justify-center w-16 h-16 rounded-full bg-green-500 text-white text-4xl transition-transform duration-200 hover:translate-y-[-3px]">
                                <ion-icon name="person-sharp"></ion-icon>
                            </a>
                        </div>
                        <div class="mt-2 text-sm font-medium text-gray-700 menu-name">
                            <span>Profil</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center text-center item-menu">
                        <div class="menu-icon">
                            <a href="{{route('absen.izin')}}" class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500 text-white text-4xl transition-transform duration-200 hover:translate-y-[-3px]">
                                <ion-icon name="calendar-number"></ion-icon>
                            </a>
                        </div>
                        <div class="mt-2 text-sm font-medium text-gray-700 menu-name">
                            <span>Cuti</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center text-center item-menu">
                        <div class="menu-icon">
                            <a href="{{route('absen.histori')}}" class="flex items-center justify-center w-16 h-16 rounded-full bg-yellow-500 text-white text-4xl transition-transform duration-200 hover:translate-y-[-3px]">
                                <ion-icon name="document-text"></ion-icon>
                            </a>
                        </div>
                        <div class="mt-2 text-sm font-medium text-gray-700 menu-name">
                            <span>Histori</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 section">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-1">
                <div class="p-4 text-white rounded-lg shadow-md card gradasigreen">
                    <div class="flex items-center space-x-3 presencecontent">
                        <div class="flex-shrink-0 iconpresence">
                            {{-- Foto Masuk akan muncul jika ada latestEntry dan jam_masuk tidak null --}}
                            @if($latestEntry !== null && $latestEntry->jam_masuk !== null)
                                @php $path=Storage::url('/uploads/absensi/' . $latestEntry->foto_masuk); @endphp
                                <img src="{{url ($path)}}" alt="Foto Masuk" class="object-cover w-12 h-12 border-2 border-white rounded-full">
                            @else
                                <ion-icon name="camera" class="text-4xl"></ion-icon>
                            @endif
                        </div>
                        <div class="presencedetail">
                            <h4 class="text-lg font-semibold presencetitle">Masuk</h4>
                            <span class="text-sm">{{$latestEntry !== null && $latestEntry->jam_masuk !== null ? $latestEntry->jam_masuk : 'Belum absen.'}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <div class="p-4 text-white rounded-lg shadow-md card gradasired">
                    <div class="flex items-center space-x-3 presencecontent">
                        <div class="flex-shrink-0 iconpresence">
                            {{-- Foto Keluar akan muncul jika ada latestEntry dan jam_keluar tidak null --}}
                            @if($latestEntry !== null && $latestEntry->jam_keluar !== null)
                                @php $path=Storage::url('/uploads/absensi/' . $latestEntry->foto_keluar); @endphp
                                <img src="{{url ($path)}}" alt="Foto Keluar" class="object-cover w-12 h-12 border-2 border-white rounded-full">
                            @else
                                <ion-icon name="camera" class="text-4xl"></ion-icon>
                            @endif
                        </div>
                        <div class="presencedetail">
                            <h4 class="text-lg font-semibold presencetitle">Keluar</h4>
                            <span class="text-sm">{{$latestEntry !== null && $latestEntry->jam_keluar !== null ? $latestEntry->jam_keluar : 'Belum absen.'}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 section" id="rekap-absen">
        <h3 class="mb-3 text-lg font-semibold text-gray-800">Rekap Absen Bulan {{ $namaBulan[$bulan] }} Tahun {{ $tahun }}</h3>
        <div class="grid grid-cols-3 gap-2">
            <div class="relative flex flex-col items-center justify-center h-20 p-2 bg-white rounded-lg shadow-md">
                <small class="absolute px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full top-1 right-2">{{ $rekapAbsen->jumlah_hadir }}</small>
                <ion-icon name="accessibility-outline" class="text-3xl text-blue-500"></ion-icon>
                <span class="mt-1 text-xs text-gray-700">Hadir</span>
            </div>
            <div class="relative flex flex-col items-center justify-center h-20 p-2 bg-white rounded-lg shadow-md">
                <small class="absolute px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full top-1 right-2">{{$rekapIzin->jumlah_izin ? $rekapIzin->jumlah_izin : 0}}</small>
                <ion-icon name="newspaper-outline" class="text-3xl text-green-500"></ion-icon>
                <span class="mt-1 text-xs text-gray-700">Izin</span>
            </div>
            <div class="relative flex flex-col items-center justify-center h-20 p-2 bg-white rounded-lg shadow-md">
                <small class="absolute px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full top-1 right-2">{{$rekapIzin->jumlah_sakit ? $rekapIzin->jumlah_sakit : 0}}</small>
                <ion-icon name="medkit-outline" class="text-3xl text-yellow-500"></ion-icon>
                <span class="mt-1 text-xs text-gray-700">Sakit</span>
            </div>
        </div>
    </div>

    <div class="mt-4 presencetab">
        <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="flex border-b border-gray-200 nav-tabs style1">
                <li class="flex-1 text-center nav-item">
                    <a class="block py-3 font-semibold text-gray-700 transition-colors duration-200 border-b-2 border-green-500 nav-link active" data-toggle="tab" href="#home" role="tab">
                        Bulan Ini
                    </a>
                </li>
                @if(auth()->user()->jabatan === 'SUPERADMIN' || auth()->user()->jabatan == 'TEAM WAGNER')
                <li class="flex-1 text-center nav-item">
                    <a class="block py-3 font-semibold text-gray-700 transition-colors duration-200 border-b-2 border-transparent nav-link hover:border-green-500" data-toggle="tab" href="#daftar" role="tab">
                        Daftar Hadir
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <div class="mt-4 tab-content" style="margin-bottom: 100px">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <ul class="overflow-hidden bg-white rounded-lg shadow-md listview">
                    @foreach ($absenBulan as $bulanData )
                    @php
                    $path = Storage::url('uploads/absensi/' . $bulanData->foto_masuk);
                    @endphp
                    <li class="flex items-center p-4 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center w-full item">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-4 overflow-hidden bg-blue-500 rounded-lg icon-box">
                                {{-- Pastikan foto_masuk ada sebelum menampilkan --}}
                                @if(!empty($bulanData->foto_masuk))
                                <img src="{{ url($path)}}" alt="Foto Masuk" class="object-cover w-full h-full rounded-lg">
                                @else
                                <ion-icon name="image-outline" class="text-2xl text-white"></ion-icon> {{-- Placeholder icon --}}
                                @endif
                            </div>
                            <div class="flex items-center justify-between flex-grow in">
                                <div class="font-medium text-gray-800">{{ date('d-m-Y', strtotime($bulanData->tanggal)) }}</div>
                                <div class="grid grid-cols-1 gap-1 text-right">
                                    <span class="px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded-full badge badge-success">{{$bulanData->jam_masuk}}</span>
                                    <span class="px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded-full badge badge-danger">{{$bulanData !== null && $bulanData->jam_keluar !== null ? $bulanData->jam_keluar : 'Belum absen.'}}</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="tab-pane fade" id="daftar" role="tabpanel">
                <ul class="overflow-hidden bg-white rounded-lg shadow-md listview">
                    @foreach ($daftarHadir as $daftar )
                    @php
                    $path = Storage::url('uploads/absensi/' . $daftar->foto_masuk);
                    @endphp
                    <li class="flex items-center p-4 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center w-full item">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-4 overflow-hidden bg-blue-500 rounded-lg icon-box">
                                {{-- Pastikan foto_masuk ada sebelum menampilkan --}}
                                @if(!empty($daftar->foto_masuk))
                                <img src="{{ url($path)}}" alt="Foto Masuk" class="object-cover w-full h-full rounded-lg">
                                @else
                                <ion-icon name="image-outline" class="text-2xl text-white"></ion-icon> {{-- Placeholder icon --}}
                                @endif
                            </div>
                            <div class="flex items-center justify-between flex-grow in">
                                <div class="font-medium text-gray-800">{{$daftar->nama}}</div>
                                <span class="px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded-full badge badge-success">{{$daftar->jam_masuk}}</span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simulate loader
        const loader = document.getElementById('loader');
        loader.classList.remove('hidden');
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 500); // Hide loader after 0.5 seconds

        // Tab functionality
        const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');
        const tabPanes = document.querySelectorAll('.tab-content .tab-pane');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();

                // Remove active class from all links and panes
                tabLinks.forEach(l => {
                    l.classList.remove('active', 'border-green-500');
                    l.classList.add('border-transparent');
                });
                tabPanes.forEach(p => {
                    p.classList.remove('show', 'active');
                });

                // Add active class to clicked link
                this.classList.add('active', 'border-green-500');
                this.classList.remove('border-transparent');

                // Show corresponding tab pane
                const targetId = this.getAttribute('href');
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });
    });
</script>
<style>
    /* Custom gradients for presence cards */
    .gradasigreen {
        background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%);
    }
    .gradasired {
        background: linear-gradient(135deg, #F44336 0%, #FF9800 100%);
    }
</style>
@endpush
