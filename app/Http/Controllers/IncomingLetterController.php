<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomingLetterController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisiasi Query Database
        $query = IncomingLetter::query();

        // 2. Fitur Pencarian (Cari No. Surat, Pengirim, atau Perihal)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        // 3. Fitur Filter (Berdasarkan Jenis Surat: Internal/Eksternal)
        if ($request->filled('filter_jenis')) {
            $query->where('jenis_surat', $request->filter_jenis);
        }

        // 4. Fitur Urutkan (Berdasarkan Tanggal Masuk Terlama/Terbaru)
        if ($request->filled('sort_by') && $request->sort_by == 'terlama') {
            $query->oldest('tgl_masuk');
        } else {
            $query->latest('tgl_masuk'); // Default: Terbaru
        }

        // Eksekusi Query
        // Eksekusi Query dengan Paginasi (15 baris per halaman)
        $surat = $query->paginate(15)->withQueryString();

        // Logika Generate No. Agenda Otomatis (001 - 999) tetap dipertahankan
        $lastSurat = IncomingLetter::orderBy('id', 'desc')->first();
        if (!$lastSurat) {
            $nextAgenda = '001';
        } else {
            $lastNumber = (int) $lastSurat->no_agenda;
            $nextAgenda = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return view('surat-masuk.index', compact('surat', 'nextAgenda'));
    }

    public function create()
    {
        // Menampilkan form tambah surat (akan kita buat setelah ini)
        return view('surat-masuk.create');
    }


   public function store(Request $request)
    {
        $request->validate([
            'no_agenda' => 'required|unique:incoming_letters,no_agenda',
            'no_surat' => 'required|string|max:255',
            'tgl_surat' => 'required|date',
            'tgl_masuk' => 'required|date',
            'jenis_surat' => 'required|in:internal,eksternal',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tujuan_head' => 'required|in:RHE,OH,BSH', // Tambahan dari desain UI Anda
            'file_surat' => 'nullable|mimes:pdf|max:5120', 
        ]);

        $filePath = null;
        if ($request->hasFile('file_surat')) {
            $filePath = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        // 1. Simpan Surat
        $surat = IncomingLetter::create([
            'no_agenda' => $request->no_agenda,
            'no_surat' => $request->no_surat,
            'tgl_surat' => $request->tgl_surat,
            'tgl_masuk' => $request->tgl_masuk,
            'jenis_surat' => $request->jenis_surat,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'file_surat' => $filePath,
            'status_surat' => 'didisposisikan', // Langsung update status
            'created_by' => Auth::id(),
        ]);

        // 2. Langsung Generate Disposisi Otomatis
        \App\Models\Disposition::create([
            'incoming_letter_id' => $surat->id,
            'tujuan_head' => $request->tujuan_head,
            'status_disposisi' => 'by_elemen',
        ]);

        $validatedData = $request->validate([
            // ... (validasi Anda yang lain tetap biarkan)
            'file_surat' => 'nullable|mimes:pdf|max:5120', // Wajib PDF, Maksimal 5MB
        ]);

        if ($request->hasFile('file_surat')) {
            $validatedData['file_surat'] = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tambah Surat Masuk',
            'description' => Auth::user()->name . ' menambahkan surat dari ' . $surat->pengirim . ' (Perihal: ' . $surat->perihal . ')'
        ]);

        return redirect()->route('surat-masuk.index')->with('success', 'Surat Masuk & Disposisi berhasil ditambahkan!');
    }
    
    public function edit($id)
    {
        $surat = IncomingLetter::findOrFail($id);
        return view('surat-masuk.edit', compact('surat'));
    }

    public function update(Request $request, $id)
    {
        $surat = IncomingLetter::findOrFail($id);

        // 1. Validasi gabungan (Teks + File)
        $request->validate([
            'no_surat' => 'required|string|max:255',
            'tgl_surat' => 'required|date',
            'jenis_surat' => 'required|in:internal,eksternal',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string',
            'file_surat' => 'nullable|mimes:pdf|max:5120',
        ]);

        // 2. Timpa data teks
        $surat->no_surat = $request->no_surat;
        $surat->tgl_surat = $request->tgl_surat;
        $surat->jenis_surat = $request->jenis_surat;
        $surat->pengirim = $request->pengirim;
        $surat->perihal = $request->perihal;

        // 3. Proses File PDF jika Krani meng-upload file baru
        if ($request->hasFile('file_surat')) {
            // Hapus file lama fisik di server (jika sebelumnya sudah ada file)
            if ($surat->file_surat) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($surat->file_surat);
            }
            // Simpan file baru dan catat path-nya
            $surat->file_surat = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        // 4. Eksekusi simpan ke Database
        $surat->save();

       ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Edit Surat Masuk',
            'description' => Auth::user()->name . ' mengubah data surat dari ' . $surat->pengirim . ' (Perihal: ' . $surat->perihal . ')'
        ]);

        // 5. Kembalikan respons AJAX
        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Data Surat Masuk berhasil diperbarui!']);
        }
        return redirect()->route('surat-masuk.index')->with('success', 'Data Surat Masuk berhasil diperbarui!');
    }
    public function destroy($id)
    {
        $surat = IncomingLetter::findOrFail($id);
        
        // Hapus file fisik PDF dari storage jika ada
        if ($surat->file_surat) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($surat->file_surat);
        }
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus Surat Masuk',
            'description' => Auth::user()->name . ' menghapus surat dari ' . $surat->pengirim . ' (Perihal: ' . $surat->perihal . ')'
        ]);

        $surat->delete(); // Ini otomatis akan menghapus data disposisinya juga karena kita set onDelete('cascade') di migration

        return redirect()->back()->with('success', 'Surat Masuk berhasil dihapus permanen!');
    }

    public function updateCatatan(Request $request, $id)
    {
        // Tolak mentah-mentah jika yang akses bukan Admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Anda tidak memiliki akses!'], 403);
        }

        $surat = IncomingLetter::findOrFail($id);
        
        $request->validate([
            'catatan_admin' => 'nullable|string'
        ]);

        $surat->catatan_admin = $request->catatan_admin;
        $surat->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Catatan Admin',
            'description' => Auth::user()->name . ' memberi catatan pada surat dari ' . $surat->pengirim . ' (Perihal: ' . $surat->perihal . ')'
        ]);

        return response()->json(['status' => 'success', 'message' => 'Catatan berhasil disimpan!']);
    }

}