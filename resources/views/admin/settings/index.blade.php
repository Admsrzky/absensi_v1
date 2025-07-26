@extends('layouts.admin.tabler') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Pengaturan
                </div>
                <h2 class="page-title">
                    Pengaturan Jam Absensi
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
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

                <form action="{{ route('admin.settings.updateAbsenTime') }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Gunakan metode PUT untuk update --}}

                    <div class="mb-3">
                        <label for="jam_masuk_start" class="form-label">Jam Mulai Absen Masuk</label>
                        <input type="time" name="jam_masuk_start" id="jam_masuk_start" class="form-control @error('jam_masuk_start') is-invalid @enderror"
                               value="{{ old('jam_masuk_start', $settings['jam_masuk_start'] ?? '08:00:00') }}" step="1">
                        @error('jam_masuk_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jam_masuk_end" class="form-label">Jam Akhir Absen Masuk</label>
                        <input type="time" name="jam_masuk_end" id="jam_masuk_end" class="form-control @error('jam_masuk_end') is-invalid @enderror"
                               value="{{ old('jam_masuk_end', $settings['jam_masuk_end'] ?? '09:00:00') }}" step="1">
                        @error('jam_masuk_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jam_keluar_min" class="form-label">Jam Minimal Absen Keluar</label>
                        <input type="time" name="jam_keluar_min" id="jam_keluar_min" class="form-control @error('jam_keluar_min') is-invalid @enderror"
                               value="{{ old('jam_keluar_min', $settings['jam_keluar_min'] ?? '17:00:00') }}" step="1">
                        @error('jam_keluar_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
