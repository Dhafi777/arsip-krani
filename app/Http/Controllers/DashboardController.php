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

        // 4. GRAFIK PIE (TUJUAN SURAT)
        // Catatan: Jika kolom tujuan di database Anda bernama lain (misal: 'tujuan_bagian'), ganti kata 'tujuan' di bawah ini
        $tujuanSurat = IncomingLetter::selectRaw('tujuan, COUNT(*) as total')
            ->whereNotNull('tujuan')
            ->groupBy('tujuan')
            ->pluck('total', 'tujuan')
            ->toArray();

        $labelTujuan = array_keys($tujuanSurat);
        $dataTujuan = array_values($tujuanSurat);

        return view('dashboard.index', compact(
            'totalSurat', 'totalSuratHariIni', 
            'totalByElement', 'totalKeHead', 'totalKeBagian',
            'chartBulan', 'labelTujuan', 'dataTujuan'
        ));
    }
}