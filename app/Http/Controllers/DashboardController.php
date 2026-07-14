<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
    {
        // 1. METRIK KOTAK ATAS
        $totalSurat = IncomingLetter::count();
        $totalSuratHariIni = IncomingLetter::whereDate('created_at', Carbon::today())->count();

        // 2. DATA TABEL RINGKAS (STATUS DISPOSISI)
        $totalByElement = IncomingLetter::where('status_disposisi', 'By Element')->orWhereNull('status_disposisi')->count();
        $totalKeHead = IncomingLetter::where('status_disposisi', 'Diserahkan ke Head')->count();
        $totalKeBagian = IncomingLetter::where('status_disposisi', 'Diteruskan ke Bagian')->count();

        // 3. GRAFIK BATANG (SURAT PER BULAN TAHUN INI)
        $suratPerBulan = IncomingLetter::selectRaw('MONTH(tgl_surat) as bulan, COUNT(*) as total')
            ->whereYear('tgl_surat', Carbon::now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();
        
        $chartBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartBulan[] = $suratPerBulan[$i] ?? 0;
        }

        // 4. GRAFIK PIE (JENIS SURAT: Internal vs Eksternal)
        $jenisSurat = IncomingLetter::selectRaw('jenis_surat, COUNT(*) as total')
            ->whereNotNull('jenis_surat')
            ->groupBy('jenis_surat')
            ->pluck('total', 'jenis_surat')
            ->toArray();

        $labelJenis = array_keys($jenisSurat);
        $dataJenis = array_values($jenisSurat);

        // 5. GRAFIK PIE (TUJUAN SURAT DARI TABEL DISPOSITIONS)
        $tujuanSurat = \App\Models\Disposition::selectRaw('tujuan_head, COUNT(*) as total')
            ->whereNotNull('tujuan_head')
            ->groupBy('tujuan_head')
            ->pluck('total', 'tujuan_head')
            ->toArray();

        // Mapping agar singkatan jadi lebih jelas di grafik
        $namaTujuan = [
            'RHE' => 'Region Head (RH)',
            'OH' => 'Operation Head (OH)',
            'BSH' => 'Business Support Head (BSH)'
        ];

        $labelTujuan = [];
        $dataTujuan = [];
        foreach ($tujuanSurat as $kode => $total) {
            $labelTujuan[] = $namaTujuan[$kode] ?? $kode;
            $dataTujuan[] = $total;
        }

        // 3.5 GRAFIK BATANG (HARIAN: SENIN - JUMAT MINGGU INI)
        // Kunci tanggal dari Senin sampai Jumat minggu berjalan berdasarkan tgl_masuk
        $startOfWeek = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $endOfWeek = $startOfWeek->copy()->addDays(4); // Senin + 4 hari = Jumat

        $suratHarian = IncomingLetter::selectRaw('DATE(tgl_masuk) as tanggal, COUNT(*) as total')
            ->whereBetween('tgl_masuk', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal')
            ->toArray();

        $labelHarian = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $chartHarian = [];
        
        // Memastikan array berurutan dari Senin s/d Jumat (indeks 0 - 4)
        for ($i = 0; $i < 5; $i++) {
            $tgl = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $chartHarian[] = $suratHarian[$tgl] ?? 0;
        }
        // ======================================================

        // 4. GRAFIK PIE (JENIS SURAT: Internal vs Eksternal)
        // ... kode jenis surat ...

        // KEMBALI KE FILE dashboard.blade.php DENGAN TAMBAHAN VARIABEL BARU
        return view('dashboard', compact(
            'totalSurat', 'totalSuratHariIni', 
            'totalByElement', 'totalKeHead', 'totalKeBagian',
            'chartBulan', 'labelHarian', 'chartHarian', // <-- Tambahkan labelHarian & chartHarian
            'labelJenis', 'dataJenis', 'labelTujuan', 'dataTujuan'
        ));
    }
}