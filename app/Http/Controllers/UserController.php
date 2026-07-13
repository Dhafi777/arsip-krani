<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        // Gembok Mutlak: Hanya Admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang boleh mengakses halaman ini.');
        }

        // Ambil semua user yang jabatannya krani
        $users = User::where('role', 'krani')->latest()->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $krani = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'krani', // Paksa role sebagai krani
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tambah Krani',
            'description' => Auth::user()->name . ' mendaftarkan akun Krani baru bernama: ' . $krani->name
        ]);

        return redirect()->back()->with('success', 'Akun Krani berhasil didaftarkan!');
    }

    public function resetPassword(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $krani = User::findOrFail($id);
        $krani->update([
            'password' => Hash::make($request->password)
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Reset Password',
            'description' => Auth::user()->name . ' mereset password untuk akun Krani: ' . $krani->name
        ]);

        return redirect()->back()->with('success', 'Password Krani berhasil direset!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $krani = User::findOrFail($id);
        
        // Proteksi ganda agar admin tidak bunuh diri menghapus akunnya sendiri
        if ($krani->role === 'admin') {
            return redirect()->back()->with('error', 'Akun Admin tidak bisa dihapus!');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus Krani',
            'description' => Auth::user()->name . ' menghapus akun Krani: ' . $krani->name
        ]);

        $krani->delete();

        return redirect()->back()->with('success', 'Akun Krani berhasil dihapus!');
    }
}