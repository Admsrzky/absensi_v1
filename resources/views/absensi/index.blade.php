@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Overview
                </div>
                <h2 class="page-title">
                    Rekap Absen
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                {{-- Pesan Sukses dari Session --}}
                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                {{-- Form Filter Periode --}}
                <form action="{{ route('absen.setPeriode') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-3 col-lg-2">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Periode Bulan/Tahun</label>
                                <input type="month" name="tanggal" id="tanggal" class="form-control" placeholder="Pilih Bulan & Tahun" autocomplete="off" value="{{ session('periode') ? session('periode') : date('Y-m') }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-1">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                    SET
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Tabel Rekap Absen --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter table-nowrap datatable" id="dataTable">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Absen Masuk</th>
                                <th class="text-center">Posisi Masuk</th>
                                <th class="text-center">Laporan Masuk</th>
                                <th class="text-center">Foto Masuk</th>
                                <th class="text-center">Absen Keluar</th>
                                <th class="text-center">Posisi Keluar</th>
                                <th class="text-center">Laporan Keluar</th>
                                <th class="text-center">Foto Keluar</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Status Validasi</th>
                                <th class="text-center">Aksi</th> {{-- Kolom Aksi baru --}}
                            </tr>
                        </thead>
                        <tbody id="loadabsen">
                            @forelse ($absen as $a)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $a->nama }}</td>
                                <td class="text-center">{{ $a->jam_masuk }}</td>
                                <td>
                                    @if($a->lokasi_masuk)
                                        <a href="#" class="btn btn-sm btn-outline-primary show-map" data-lokasi="{{ $a->lokasi_masuk }}" title="Lihat Lokasi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $a->laporan_masuk ?: '-' }}</td>
                                <td class="text-center">
                                    @if($a->foto_masuk)
                                        @php $fotoIn = Storage::url('uploads/absensi/' . $a->foto_masuk); @endphp
                                        <a href="{{ url($fotoIn) }}" target="_blank">
                                            <img src="{{ url($fotoIn) }}" alt="Foto Masuk" class="rounded avatar avatar-sm">
                                        </a>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $a->jam_keluar ?: '-' }}</td>
                                <td>
                                    @if($a->lokasi_keluar)
                                        <a href="#" class="btn btn-sm btn-outline-primary show-map" data-lokasi="{{ $a->lokasi_keluar }}" title="Lihat Lokasi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $a->laporan_keluar ?: '-' }}</td>
                                <td class="text-center">
                                    @if($a->foto_keluar)
                                        @php $fotoOut = Storage::url('uploads/absensi/' . $a->foto_keluar); @endphp
                                        <a href="{{ url($fotoOut) }}" target="_blank">
                                            <img src="{{ url($fotoOut) }}" alt="Foto Keluar" class="rounded avatar avatar-sm">
                                        </a>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ date('d-m-Y', strtotime($a->tanggal)) }}</td>
                                <td class="text-center">
                                    <a href="#" class="btn-update-validation"
                                       data-id="{{ $a->id }}"
                                       data-current-status="{{ $a->status_validasi }}">
                                        @if($a->status_validasi == 1)
                                            <i class="fa-solid fa-circle-check text-success" title="Disetujui"></i>
                                        @elseif($a->status_validasi == 2)
                                            <i class="fa-solid fa-circle-xmark text-danger" title="Ditolak"></i>
                                        @else
                                            <i class="fa-solid fa-hourglass-half text-warning" title="Pending"></i>
                                        @endif
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-icon btn-edit-absen"
                                        data-id="{{ $a->id }}"
                                        data-email="{{ $a->email }}"
                                        data-nama="{{ $a->nama }}"
                                        data-tanggal="{{ $a->tanggal }}"
                                        data-jammasuk="{{ $a->jam_masuk }}"
                                        data-jamkeluar="{{ $a->jam_keluar }}"
                                        data-statusvalidasi="{{ $a->status_validasi }}"
                                        title="Edit Absen">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M10.707 11.293a1 1 0 0 1 0 1.414l-2 2a1 1 0 0 1 -1.414 0l-2 -2a1 1 0 0 1 0 -1.414l2 -2a1 1 0 0 1 1.414 0z" /><path d="M14 6l4 4" /><path d="M21 11.5v-2.5a2 2 0 0 0 -2 -2h-2.5l-4 -4l-1.5 1.5l4 4z" /></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center">Tidak ada data absensi untuk periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk menampilkan peta --}}
<div class="modal modal-blur fade" id="modal-showmap" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lokasi Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map-modal" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Edit Absen --}}
<div class="modal modal-blur fade" id="modal-edit-absen" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-absen">
                    @csrf
                    <input type="hidden" name="id" id="edit_absen_id">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit_nama" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="edit_tanggal" name="tanggal" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Masuk</label>
                        <input type="time" class="form-control" id="edit_jam_masuk" name="jam_masuk">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Keluar</label>
                        <input type="time" class="form-control" id="edit_jam_keluar" name="jam_keluar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Validasi</label>
                        <select class="form-select" id="edit_status_validasi" name="status_validasi">
                            <option value="0">Pending</option>
                            <option value="1">Setujui</option>
                            <option value="2">Tolak</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    // Inisialisasi DataTable
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[10, "desc"]] // Urutkan berdasarkan kolom Tanggal (indeks 10) secara descending
        });

        // Event listener untuk tombol "Lihat Lokasi"
        $(document).on('click', '.show-map', function(e) {
            e.preventDefault();
            var lokasi = $(this).data('lokasi');
            var coords = lokasi.split('|')[0].split(',');
            var latitude = parseFloat(coords[0]);
            var longitude = parseFloat(coords[1]);

            var mapModal = $('#map-modal');
            mapModal.empty(); // Bersihkan konten peta sebelumnya

            // Inisialisasi peta di modal
            var map = L.map('map-modal').setView([latitude, longitude], 17); // Zoom level 17

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([latitude, longitude]).addTo(map)
                .bindPopup('Lokasi Absensi: ' + lokasi.split('|')[1]).openPopup();

            $('#modal-showmap').modal('show');

            // Penting: Invalidate size setelah modal terbuka sepenuhnya
            $('#modal-showmap').on('shown.bs.modal', function() {
                map.invalidateSize();
            });
        });

        // Event listener untuk tombol "Edit Absen"
        $(document).on('click', '.btn-edit-absen', function(e) {
            e.preventDefault();
            const absenId = $(this).data('id');
            const email = $(this).data('email');
            const nama = $(this).data('nama');
            const tanggal = $(this).data('tanggal');
            const jamMasuk = $(this).data('jammasuk');
            const jamKeluar = $(this).data('jamkeluar');
            const statusValidasi = $(this).data('statusvalidasi');

            // Isi form di modal
            $('#edit_absen_id').val(absenId);
            $('#edit_email').val(email);
            $('#edit_nama').val(nama);
            $('#edit_tanggal').val(tanggal);
            $('#edit_jam_masuk').val(jamMasuk);
            $('#edit_jam_keluar').val(jamKeluar);
            $('#edit_status_validasi').val(statusValidasi);

            $('#modal-edit-absen').modal('show');
        });

        // Handler untuk submit form edit absensi
        $('#form-edit-absen').submit(function(e) {
            e.preventDefault();
            const absenId = $('#edit_absen_id').val();
            const newJamMasuk = $('#edit_jam_masuk').val();
            const newJamKeluar = $('#edit_jam_keluar').val();
            const newStatusValidasi = $('#edit_status_validasi').val();

            // SweetAlert2 confirmation
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menyimpan perubahan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('absen.update', ':id') }}".replace(':id', absenId), // Menggunakan rute update
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "PUT", // Penting untuk metode PUT di Laravel
                            jam_masuk: newJamMasuk,
                            jam_keluar: newJamKeluar,
                            status_validasi: newStatusValidasi,
                            // Kirim juga data email dan nama karena dibutuhkan oleh validasi backend (jika ada)
                            email: $('#edit_email').val(),
                            nama: $('#edit_nama').val(),
                            // Keterangan dan posisi_absen mungkin perlu dikirim jika model require/validasi
                            // Namun, jika hanya jam dan status validasi yang diubah, bisa diabaikan
                            keterangan: '', // Default kosong, jika tidak ada input di form
                            posisi_absen: '', // Default kosong, jika tidak ada input di form
                            absen_masuk: newJamMasuk, // Map to existing field in controller
                            absen_keluar: newJamKeluar, // Map to existing field in controller
                        },
                        success: function(response) {
                            Swal.fire(
                                'Berhasil!',
                                'Data absensi berhasil diperbarui.',
                                'success'
                            ).then(() => {
                                $('#modal-edit-absen').modal('hide');
                                location.reload(); // Reload the page to see changes
                            });
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'Terjadi kesalahan saat memperbarui data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                errorMessage = xhr.responseText;
                            }
                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                            console.error("AJAX error: ", xhr.responseText);
                        }
                    });
                }
            });
        });

        // Event listener untuk mengubah status validasi langsung dari ikon
        $(document).on('click', '.btn-update-validation', function(e) {
            e.preventDefault();
            const absenId = $(this).data('id');
            const currentStatus = $(this).data('current-status');

            const inputOptions = new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        '0': 'Pending',
                        '1': 'Setujui',
                        '2': 'Tolak'
                    })
                }, 50)
            })

            Swal.fire({
                title: 'Ubah Status Validasi',
                input: 'select',
                inputOptions: inputOptions,
                inputValue: currentStatus, // Set default selected value
                inputPlaceholder: 'Pilih status baru',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        if (value !== '') {
                            resolve()
                        } else {
                            resolve('Anda harus memilih status!')
                        }
                    })
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const newStatus = result.value;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('absen.updateValidationStatus') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: absenId,
                            status_validasi: newStatus
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                            console.error("AJAX error: ", xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
```
