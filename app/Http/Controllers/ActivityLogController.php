<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Gembok mutlak: Tendang Krani jika nekat mengakses URL ini via browser
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Halaman ini murni khusus Admin.');
        }

        // Ambil data log berserta nama user, urutkan dari yang terbaru
        $logs = ActivityLog::with('user')->latest()->paginate(15);
        
        return view('riwayat_aksi.index', compact('logs'));
    }
}