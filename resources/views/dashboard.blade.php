@extends('layouts.master')

@section('content')
<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="h-full w-full overflow-y-auto bg-[#f8fafc] p-2">
    <!-- BARIS 1: METRIK UTAMA (KOTAK ANGKA ALA CRYPTO) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Card 1: Total Surat -->
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 border border-gray-50 flex justify-between items-center transition hover:-translate-y-1 duration-300">
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Total Surat Masuk</p>
                <h3 class="text-4xl font-extrabold text-gray-800">{{ $totalSurat }}</h3>
            </div>
            <div class="p-4 bg-green-50 rounded-xl">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <!-- Card 2: Surat Hari Ini -->
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 border border-gray-50 flex justify-between items-center transition hover:-translate-y-1 duration-300">
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Surat Masuk Hari Ini</p>
                <h3 class="text-4xl font-extrabold text-gray-800">{{ $totalSuratHariIni }}</h3>
            </div>
            <div class="p-4 bg-blue-50 rounded-xl">
                <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>
    </div>

    <!-- BARIS 2: GRAFIK OVERVIEW & STATISTIK DISPOSISI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Grafik Batang Kiri (Market Overview Style) -->
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 border border-gray-50 lg:col-span-2">
            <h2 class="text-lg font-extrabold text-gray-800 mb-4">Statistik Surat Masuk 2026</h2>
            <div class="w-full h-72">
                <canvas id="barBulan"></canvas>
            </div>
        </div>

        <!-- Tabel Ringkas / Status Disposisi (Balances Style) -->
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 border border-gray-50 flex flex-col">
            <h2 class="text-lg font-extrabold text-gray-800 mb-6">Status Disposisi</h2>
            
            <div class="space-y-4 flex-1">
                <!-- Item 1 -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                        <span class="font-bold text-gray-600 text-sm">By Element</span>
                    </div>
                    <span class="font-extrabold text-gray-800">{{ $totalByElement }}</span>
                </div>

                <!-- Item 2 -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 hover:bg-red-100 transition border border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="font-bold text-red-700 text-sm">Ke Head</span>
                    </div>
                    <span class="font-extrabold text-red-800">{{ $totalKeHead }}</span>
                </div>

                <!-- Item 3 -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-green-50 hover:bg-green-100 transition border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="font-bold text-green-700 text-sm">Ke Bagian</span>
                    </div>
                    <span class="font-extrabold text-green-800">{{ $totalKeBagian }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- BARIS 3: GRAFIK PIE (TUJUAN & STATUS) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pie Chart Tujuan -->
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 border border-gray-50">
            <h2 class="text-lg font-extrabold text-gray-800 mb-4 text-center">Distribusi Jenis Surat</h2>
            <div class="w-full h-64 flex justify-center">
                <canvas id="pieJenis"></canvas>
            </div>
        </div>

        <!-- Pie Chart Status Disposisi -->
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 border border-gray-50">
            <h2 class="text-lg font-extrabold text-gray-800 mb-4 text-center">Rasio Status Disposisi</h2>
            <div class="w-full h-64 flex justify-center">
                <canvas id="pieStatus"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 border border-gray-50">
            <h2 class="text-lg font-extrabold text-gray-800 mb-4 text-center">Distribusi Tujuan Surat</h2>
            <div class="w-full h-64 flex justify-center">
                <canvas id="pieTujuan"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- INISIALISASI CHART.JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Grafik Batang (Surat per Bulan)
    const ctxBulan = document.getElementById('barBulan').getContext('2d');
    
    // Membuat gradient gaya crypto untuk bar chart
    let gradientBar = ctxBulan.createLinearGradient(0, 0, 0, 400);
    gradientBar.addColorStop(0, 'rgba(34, 197, 94, 0.8)'); // Hijau PTPN
    gradientBar.addColorStop(1, 'rgba(34, 197, 94, 0.2)');

    new Chart(ctxBulan, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Jumlah Surat',
                data: @json($chartBulan),
                backgroundColor: gradientBar,
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f3f4f6' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Grafik Pie (Tujuan Surat)
    // 2. Grafik Pie (Jenis Surat: Internal vs Eksternal)
    const ctxJenis = document.getElementById('pieJenis').getContext('2d');
    new Chart(ctxJenis, {
        type: 'doughnut',
        data: {
            labels: @json($labelJenis).map(label => label.charAt(0).toUpperCase() + label.slice(1)), // Kapital huruf pertama
            datasets: [{
                data: @json($dataJenis),
                backgroundColor: ['#3b82f6', '#f59e0b'], // Biru untuk internal, Kuning/Orange untuk eksternal
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
    });

    // 3. Grafik Pie (Status Disposisi)
    const ctxStatus = document.getElementById('pieStatus').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['By Element', 'Ke Head', 'Ke Bagian'],
            datasets: [{
                data: [{{ $totalByElement }}, {{ $totalKeHead }}, {{ $totalKeBagian }}],
                backgroundColor: ['#9ca3af', '#ef4444', '#22c55e'], // Abu, Merah, Hijau
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
    });

    // Grafik Pie (Tujuan Surat)
    const ctxTujuan = document.getElementById('pieTujuan').getContext('2d');
    new Chart(ctxTujuan, {
        type: 'doughnut',
        data: {
            labels: @json($labelTujuan),
            datasets: [{
                data: @json($dataTujuan),
                backgroundColor: ['#1e40af', '#b91c1c', '#6b21a8', '#f59e0b'], // Biru RHE, Merah OH, Ungu BSH
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
    });
});
</script>
@endsection