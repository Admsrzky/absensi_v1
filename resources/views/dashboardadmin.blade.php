@extends('layouts.admin.tabler')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    Overview
                </div>
                <h2 class="page-title">
                    Dashboard Admin
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">

            {{-- Card Total Karyawan --}}
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="text-white bg-primary avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{$user}}
                                </div>
                                <div class="text-secondary">
                                    Total Karyawan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Karyawan Hadir --}}
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="text-white bg-success avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-fingerprint" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3" />
                                        <path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6" />
                                        <path d="M12 11v2a14 14 0 0 0 2.5 8" />
                                        <path d="M8 15a18 18 0 0 0 1.8 6" />
                                        <path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $rekapAbsen->jumlah_hadir }}
                                </div>
                                <div class="text-secondary">
                                    Karyawan Hadir
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Karyawan Izin --}}
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="text-white bg-info avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-text" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 9l1 0" />
                                        <path d="M9 13l6 0" />
                                        <path d="M9 17l6 0" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{$rekapIzin->jumlah_izin ?? 0}}
                                </div>
                                <div class="text-secondary">
                                    Karyawan Izin
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Karyawan Sakit --}}
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="text-white bg-warning avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mood-sick" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18z" />
                                        <path d="M9 10h-.01" />
                                        <path d="M15 10h-.01" />
                                        <path d="M8 16l1 -1l1.5 1l1.5 -1l1.5 1l1.5 -1l1 1" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{$rekapIzin->jumlah_sakit ?? 0}}
                                </div>
                                <div class="text-secondary">
                                    Karyawan Sakit
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Chart Section --}}
        <div class="mt-4 row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Rekapitulasi Kehadiran Bulan {{ $namaBulan[$bulan] }} Tahun {{ $tahun }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekapitulasi Table --}}
        <div class="mt-4 row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Rekapitulasi Kehadiran Bulan Ini</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Karyawan</th>
                                        <th>Hadir</th>
                                        <th>Izin</th>
                                        <th>Sakit</th>
                                        <th>Total Jam Kerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop through data for each user if available --}}
                                    @forelse($rekapitulasiPerUser as $rekapUser)
                                    <tr>
                                        <td>{{ $rekapUser['nama'] }}</td>
                                        <td>{{ $rekapUser['jumlah_hadir'] }}</td>
                                        <td>{{ $rekapUser['jumlah_izin'] }}</td>
                                        <td>{{ $rekapUser['jumlah_sakit'] }}</td>
                                        <td>{{ $rekapUser['total_jam_kerja'] }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data rekapitulasi untuk bulan ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('myscript') {{-- DIUBAH: dari @push('scripts') menjadi @push('myscript') --}}
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data for the chart
        const hadir = {{ $rekapAbsen->jumlah_hadir }};
        const izin = {{ $rekapIzin->jumlah_izin ?? 0 }};
        const sakit = {{ $rekapIzin->jumlah_sakit ?? 0 }};
        const alpha = {{ $alphaCount ?? 0 }};

        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar', // You can change this to 'pie', 'line', etc.
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: [hadir, izin, sakit, alpha],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)', // Green for Hadir
                        'rgba(23, 162, 184, 0.8)', // Info Blue for Izin
                        'rgba(255, 193, 7, 0.8)', // Warning Yellow for Sakit
                        'rgba(220, 53, 69, 0.8)' // Danger Red for Alpha
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // Ensure Y-axis shows whole numbers
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Hide legend if labels are clear
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' orang';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
