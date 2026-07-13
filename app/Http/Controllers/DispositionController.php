<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class DispositionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Siapkan kerangka query dasar
        $query = IncomingLetter::query();

        // 2. Jika ada request pencarian (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        // 3. Jika ada request filter dari dropdown (Status Disposisi)
        if ($request->filled('filter')) {
            $query->where('status_disposisi', $request->filter);
        }

        // 4. Eksekusi query (urutkan yang terbaru, lalu ambil datanya)
        $surat = $query->latest('tgl_masuk')->get();

        return view('disposisi.index', compact('surat'));
    }

    public function update(Request $request, $id)
    {
        $surat = IncomingLetter::findOrFail($id);

        // 1. Siapkan aturan dasar
        $rules = [
            'status_disposisi' => 'required|in:By Element,Diserahkan ke Head,Diteruskan ke Bagian',
        ];

        // 2. Logika Revisi: Wajib upload HANYA JIKA status = Diteruskan ke Bagian
        if ($request->status_disposisi === 'Diteruskan ke Bagian') {
            $rules['file_hasil_disposisi'] = 'required|mimes:pdf|max:5120';
        } else {
            $rules['file_hasil_disposisi'] = 'nullable|mimes:pdf|max:5120';
        }

        // 3. Eksekusi Validasi dengan pesan custom agar Krani paham
        $request->validate($rules, [
            'file_hasil_disposisi.required' => 'File hasil disposisi WAJIB di-upload untuk status Diteruskan ke Bagian!'
        ]);

        $surat->status_disposisi = $request->status_disposisi;

        // Proses unggah file
        if ($request->hasFile('file_hasil_disposisi')) {
            if ($surat->file_hasil_disposisi) {
                Storage::disk('public')->delete($surat->file_hasil_disposisi);
            }
            $surat->file_hasil_disposisi = $request->file('file_hasil_disposisi')->store('hasil_disposisi', 'public');
        }

        $surat->save();

        // Pastikan variabel $surat ini sesuai dengan nama variabel yang Anda gunakan di controller Anda
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Disposisi',
            'description' => Auth::user()->name . ' memperbarui status disposisi surat dari ' . $surat->pengirim . ' (Perihal: ' . $surat->perihal . ') menjadi: ' . $surat->status_disposisi
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Status dan File Disposisi berhasil diperbarui!']);
        }
        return redirect()->back()->with('success', 'Berhasil update!');
    }

    public function cetak($id)
    {
        $surat = IncomingLetter::findOrFail($id);
        return view('disposisi.cetak', compact('surat'));
    }
}