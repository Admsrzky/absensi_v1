@extends('layouts.app')

@section('header')
{{-- No custom styles needed here if using Tailwind --}}
@endsection

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="min-h-screen overflow-hidden bg-gray-900 rounded-lg shadow-lg">
        <div class="p-4">
            <h2 class="mb-4 text-xl font-semibold text-white">Absensi</h2>
            <input type="hidden" name="lokasi" id="lokasi">

            {{-- Webcam.js will render the video stream here --}}
            <div class="flex items-center justify-center w-full mb-4 overflow-hidden text-gray-400 bg-gray-800 rounded-md webcam-capture aspect-video">
                <p>Loading webcam...</p>
            </div>

            {{-- Optional: Display a canvas for captured image if needed for preview --}}
            {{-- <div id="canvasContainer" class="grid grid-cols-3 gap-2 mb-4"></div> --}}

            <div id="map" class="w-full h-64 mb-6 rounded-md shadow-md"></div>

            <div class="pb-20"> {{-- Add padding-bottom to ensure button is above bottom menu --}}
                @if(isset($cek) && $cek->jam_keluar === null && $selisihWaktu < 15)
                {{-- Show Absen Keluar if already clocked in today and not yet clocked out --}}
                <button id="takeAbsenKeluar"
                        onclick="captureAndSubmit('keluar')"
                        class="flex items-center justify-center w-full py-3 font-bold text-white transition duration-300 ease-in-out bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                    <ion-icon name="camera-outline" class="mr-2 text-xl align-middle"></ion-icon>
                    Absen Keluar
                </button>
                @else
                {{-- Show Absen Masuk otherwise (initial state or after full day cycle) --}}
                <button id="takeAbsenMasuk"
                        onclick="captureAndSubmit('masuk')"
                        class="flex items-center justify-center w-full py-3 font-bold text-white transition duration-300 ease-in-out rounded-lg bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50">
                    <ion-icon name="camera-outline" class="mr-2 text-xl align-middle"></ion-icon>
                    Absen Masuk
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    // Initialize map with a default view (e.g., Cilegon, Indonesia)
    var map = L.map('map').setView([-6.0076, 106.0526], 13); // Centered on Cilegon, Banten

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var lokasiInput = document.getElementById('lokasi');
    var currentMarker; // To store the current location marker

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Geolocation is not supported by your browser.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    function showPosition(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        var accuracy = position.coords.accuracy; // Accuracy in meters

        // Remove previous marker if it exists
        if (currentMarker) {
            map.removeLayer(currentMarker);
        }

        // Add a new marker with a popup showing coordinates and accuracy
        currentMarker = L.marker([latitude, longitude]).addTo(map)
            .bindPopup(`<b>Lokasi Anda:</b><br>Lat: ${latitude.toFixed(5)}<br>Long: ${longitude.toFixed(5)}<br>Akurasi: ${accuracy.toFixed(2)} meter`).openPopup();

        // Add a circle representing the accuracy
        var circle = L.circle([latitude, longitude], {
            color: '#3498db',
            fillColor: '#3498db',
            fillOpacity: 0.2,
            radius: accuracy
        }).addTo(map);

        // Set the map view to the current location with a slightly higher zoom
        map.setView([latitude, longitude], 17); // Zoom level 17 is generally good for street level

        // Use Nominatim API to get the address
        var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                var address = data.display_name || 'Alamat tidak ditemukan';
                lokasiInput.value = `${latitude},${longitude}|${address}`; // Store both coords and address
            })
            .catch(error => {
                console.error('Error fetching address:', error);
                lokasiInput.value = `${latitude},${longitude}|Alamat tidak dapat diambil`;
            });
    }

    function showError(error) {
        let errorMessage = "Terjadi kesalahan saat mendapatkan lokasi Anda.";
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = "Izin lokasi ditolak. Mohon izinkan akses lokasi untuk absensi.";
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = "Informasi lokasi tidak tersedia.";
                break;
            case error.TIMEOUT:
                errorMessage = "Waktu permintaan lokasi habis.";
                break;
            case error.UNKNOWN_ERROR:
                errorMessage = "Terjadi kesalahan yang tidak diketahui.";
                break;
        }
        Swal.fire({
            title: 'Error Lokasi!',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'OK'
        });
        console.error("Geolocation Error: ", error);
        lokasiInput.value = "Lokasi tidak tersedia"; // Set a default value if location fails
    }

    // Call getCurrentLocation on page load
    getCurrentLocation();

    var image = ''; // Global variable to store the captured image URI

    Webcam.set({
        height: 400, // Adjusted height for better aspect ratio with 640 width
        width: 640,
        image_format: 'jpeg',
        jpeg_quality: 90, // Increased quality slightly
        flip_horiz: true
    });

    Webcam.attach('.webcam-capture'); // Attach to the div with class webcam-capture

    function captureAndSubmit(jenisAbsen) {
        // Retrieve dynamic attendance times passed from the controller
        const jamMasukStart  = "{{ $jamMasukStart }}"; // e.g., "08:00:00"
        const jamMasukEnd    = "{{ $jamMasukEnd }}";   // e.g., "09:00:00"
        const jamKeluarMin   = "{{ $jamKeluarMin }}";  // e.g., "17:00:00"

        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        const currentSecond = now.getSeconds();

        // Convert dynamic time strings to Date objects for comparison
        const [startH, startM, startS] = jamMasukStart.split(':').map(Number);
        const [endH, endM, endS]       = jamMasukEnd.split(':').map(Number);
        const [keluarH, keluarM, keluarS] = jamKeluarMin.split(':').map(Number);

        const currentTotalSeconds = currentHour * 3600 + currentMinute * 60 + currentSecond;
        const masukStartTotalSeconds = startH * 3600 + startM * 60 + startS;
        const masukEndTotalSeconds   = endH * 3600 + endM * 60 + endS;
        const keluarMinTotalSeconds  = keluarH * 3600 + keluarM * 60 + keluarS;


        if (jenisAbsen === 'masuk') {
            if (currentTotalSeconds < masukStartTotalSeconds || currentTotalSeconds > masukEndTotalSeconds) {
                Swal.fire({
                    title: 'Waktu Absen Tidak Sesuai!',
                    text: `Absen masuk hanya bisa dilakukan antara jam ${jamMasukStart.substring(0, 5)} sampai ${jamMasukEnd.substring(0, 5)}.`,
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false; // Prevent submission
            }
        } else if (jenisAbsen === 'keluar') {
            if (currentTotalSeconds < keluarMinTotalSeconds) {
                Swal.fire({
                    title: 'Waktu Absen Tidak Sesuai!',
                    text: `Absen keluar hanya bisa dilakukan pada jam ${jamKeluarMin.substring(0, 5)} atau setelahnya.`,
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false; // Prevent submission
            }
        }

        Webcam.snap(function(uri) {
            image = uri; // Store the captured image URI globally

            // Disable the button to prevent multiple submissions
            const buttonId = jenisAbsen === 'masuk' ? 'takeAbsenMasuk' : 'takeAbsenKeluar';
            $(`#${buttonId}`).prop('disabled', true);
            $(`#${buttonId}`).html('<ion-icon name="sync-outline" class="mr-2 text-xl align-middle animate-spin"></ion-icon> Memproses...');

            var lokasi = $("#lokasi").val();

            if (!lokasi || lokasi === "Lokasi tidak tersedia") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Lokasi belum terdeteksi atau izin lokasi tidak diberikan.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                $(`#${buttonId}`).prop('disabled', false); // Re-enable button
                $(`#${buttonId}`).html(`<ion-icon name="camera-outline" class="mr-2 text-xl align-middle"></ion-icon> Absen ${jenisAbsen === 'masuk' ? 'Masuk' : 'Keluar'}`);
                return false;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('absen.store') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi,
                    jenis_absen: jenisAbsen,
                },
                cache: false,
                success: function(respond) {
                    var status = respond.split("|");
                    if (status[0] == "success") {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: status[1],
                            icon: 'success',
                            confirmButtonText: 'OK',
                        }).then(() => {
                            window.location.href = '{{ route('absen.dashboard') }}';
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: status[1] || 'Maaf, absen tidak berhasil.', // Use status[1] for specific error message
                            icon: 'error',
                            confirmButtonText: 'OK',
                        }).then(() => {
                            $(`#${buttonId}`).prop('disabled', false); // Re-enable button on error
                            $(`#${buttonId}`).html(`<ion-icon name="camera-outline" class="mr-2 text-xl align-middle"></ion-icon> Absen ${jenisAbsen === 'masuk' ? 'Masuk' : 'Keluar'}`);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server: ' + xhr.responseText,
                        icon: 'error',
                        confirmButtonText: 'OK',
                    }).then(() => {
                        $(`#${buttonId}`).prop('disabled', false); // Re-enable button on AJAX error
                        $(`#${buttonId}`).html(`<ion-icon name="camera-outline" class="mr-2 text-xl align-middle"></ion-icon> Absen ${jenisAbsen === 'masuk' ? 'Masuk' : 'Keluar'}`);
                    });
                }
            });
        });
    }
</script>
@endpush
