@extends('layouts.admin.tabler') {{-- Ensure this extends your admin layout --}}

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Manajemen Data
                </div>
                <h2 class="page-title">
                    Data Jabatan
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-add-jabatan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Tambah Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                {{-- Session messages --}}
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

                {{-- Search form --}}
                <form action="{{ route('admin.jabatan.index') }}" method="GET" class="mb-4">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="search_jabatan" class="form-label">Cari Nama Jabatan</label>
                                <input type="text" name="search_jabatan" id="search_jabatan" class="form-control" placeholder="Masukkan nama jabatan" value="{{ request('search_jabatan') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Jabatan Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter table-nowrap datatable" id="jabatanTable">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">ID Jabatan</th> {{-- UNCOMMENTED: Tampilkan kolom ID Jabatan --}}
                                <th class="text-center">Nama Jabatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jabatan as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + $jabatan->firstItem() - 1 }}</td>
                                <td>{{ $item->id_jabatan }}</td> {{-- UNCOMMENTED: Tampilkan ID Jabatan --}}
                                <td>{{ $item->jabatan }}</td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-icon btn-edit-jabatan"
                                       data-id="{{ $item->id }}"
                                       data-jabatan="{{ $item->jabatan }}"
                                       data-id_jabatan="{{ $item->id_jabatan }}" {{-- PENTING: Kirim juga id_jabatan ke JS --}}
                                       title="Edit Jabatan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M10.707 11.293a1 1 0 0 1 0 1.414l-2 2a1 1 0 0 1 -1.414 0l-2 -2a1 1 0 0 1 0 -1.414l2 -2a1 1 0 0 1 1.414 0z" /><path d="M14 6l4 4" /><path d="M21 11.5v-2.5a2 2 0 0 0 -2 -2h-2.5l-4 -4l-1.5 1.5l4 4z" /></svg>
                                    </a>
                                    <form action="{{ route('admin.jabatan.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus Jabatan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data jabatan.</td> {{-- Kolom span disesuaikan --}}
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination links --}}
                <div class="mt-3">
                    {{ $jabatan->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Adding Jabatan --}}
<div class="modal modal-blur fade" id="modal-add-jabatan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jabatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.jabatan.store') }}" method="POST" id="form-add-jabatan">
                    @csrf
                    {{-- Input untuk ID Jabatan (sekarang WAJIB diisi) --}}
                    <div class="mb-3">
                        <label class="form-label">ID Jabatan</label>
                        <input type="text" name="id_jabatan" class="form-control" placeholder="Cth: JAB001" required> {{-- id_jabatan required --}}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Masukkan nama jabatan" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Editing Jabatan --}}
<div class="modal modal-blur fade" id="modal-edit-jabatan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jabatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="form-edit-jabatan">
                    @csrf
                    @method('PUT') {{-- Use PUT method for update --}}
                    <input type="hidden" name="id" id="edit_jabatan_id">
                    {{-- Input untuk ID Jabatan (sekarang WAJIB diisi) --}}
                    <div class="mb-3">
                        <label class="form-label">ID Jabatan</label>
                        <input type="text" name="id_jabatan" id="edit_id_jabatan" class="form-control" placeholder="Cth: JAB001" required> {{-- id_jabatan required --}}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jabatan</label>
                        <input type="text" name="jabatan" id="edit_jabatan" class="form-control" placeholder="Masukkan nama jabatan" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        // Handle Edit button click
        $('.btn-edit-jabatan').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const jabatanName = $(this).data('jabatan');
            const idJabatan = $(this).data('id_jabatan'); // MENGAMBIL data-id_jabatan

            $('#edit_jabatan_id').val(id);
            $('#edit_jabatan').val(jabatanName);
            $('#edit_id_jabatan').val(idJabatan); // MENGISI input ID Jabatan

            // Set form action URL dynamically for update
            const updateUrl = "{{ route('admin.jabatan.update', ':id') }}".replace(':id', id);
            $('#form-edit-jabatan').attr('action', updateUrl);

            $('#modal-edit-jabatan').modal('show');
        });

        // Handle Delete form submission with SweetAlert
        $('.delete-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data jabatan ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.off('submit').submit(); // Submit the form if confirmed
                }
            });
        });
    });
</script>
@endpush
