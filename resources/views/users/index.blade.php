@extends('layouts.master')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden h-full" 
    x-data="{ 
        activeModal: null, 
        resetId: '', resetName: '',
        deleteId: '', deleteName: ''
    }"
    @open-reset.window="resetId = $event.detail.id; resetName = $event.detail.name; activeModal = 'reset'"
    @open-delete.window="deleteId = $event.detail.id; deleteName = $event.detail.name; activeModal = 'delete'"
    @keydown.escape.window="activeModal = null">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-5 right-5 z-50 bg-green-600 text-white px-6 py-4 rounded shadow-2xl flex items-center gap-3 border-l-4 border-green-900">
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-5 right-5 z-50 bg-red-600 text-white px-6 py-4 rounded shadow-2xl flex items-center gap-3 border-l-4 border-red-900">
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex-1 flex flex-col p-8 bg-gray-50 overflow-hidden">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-black tracking-wide">MANAJEMEN KRANI</h1>
                <div class="flex h-2 w-48 mt-1">
                    <div class="w-1/4 bg-blue-900"></div><div class="w-1/4 bg-blue-500"></div><div class="w-1/4 bg-yellow-400"></div><div class="w-1/4 bg-green-500"></div>
                </div>
            </div>
            
            <button type="button" @click="activeModal = 'tambah'" class="bg-gradient-to-b from-gray-200 to-gray-400 border border-gray-400 text-black font-bold py-2 px-4 rounded shadow-md text-sm flex items-center hover:opacity-90 transition">
                <span class="mr-1 text-lg leading-none">+</span> Daftarkan Krani Baru
            </button>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 flex-1 overflow-y-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 sticky top-0 z-10 border-b-2 border-black text-black">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold">Nama Lengkap</th>
                        <th class="px-6 py-4 text-left font-bold">Email / Username</th>
                        <th class="px-6 py-4 text-left font-bold">Tanggal Didaftarkan</th>
                        <th class="px-6 py-4 text-center font-bold">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $index => $u)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $u->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $u->email }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($u->created_at)->format('d F Y - H:i') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <button type="button" @click="$dispatch('open-reset', @js(['id' => $u->id, 'name' => $u->name]))" class="text-white font-bold text-xs bg-yellow-500 hover:bg-yellow-600 py-1.5 px-3 rounded shadow transition">
                                    Reset Password
                                </button>
                                <button type="button" @click="$dispatch('open-delete', @js(['id' => $u->id, 'name' => $u->name]))" class="text-white font-bold text-xs bg-red-600 hover:bg-red-700 py-1.5 px-3 rounded shadow transition">
                                    Hapus Akun
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-6 text-gray-500 font-bold">Belum ada akun Krani yang didaftarkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="activeModal === 'tambah'" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" @click="activeModal = null"></div>
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl relative z-10 p-6 border-t-8 border-blue-600">
            <h2 class="text-xl font-extrabold mb-4 text-black">Daftarkan Krani Baru</h2>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full border-gray-400 border-2 p-2 rounded focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email / Username</label>
                    <input type="email" name="email" required class="w-full border-gray-400 border-2 p-2 rounded focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border-gray-400 border-2 p-2 rounded focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="w-full border-gray-400 border-2 p-2 rounded focus:ring-blue-500 text-sm">
                </div>
                <div class="flex justify-end pt-4 space-x-2">
                    <button type="button" @click="activeModal = null" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">Daftarkan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="activeModal === 'reset'" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" @click="activeModal = null"></div>
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl relative z-10 p-6 border-t-8 border-yellow-500">
            <h2 class="text-xl font-extrabold mb-1 text-black">Reset Password</h2>
            <p class="text-sm text-gray-600 mb-4">Set password baru untuk <span x-text="resetName" class="font-bold"></span></p>
            <form method="POST" :action="'{{ url('users') }}/' + resetId + '/reset-password'" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required class="w-full border-gray-400 border-2 p-2 rounded focus:ring-yellow-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required class="w-full border-gray-400 border-2 p-2 rounded focus:ring-yellow-500 text-sm">
                </div>
                <div class="flex justify-end pt-4 space-x-2">
                    <button type="button" @click="activeModal = null" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-yellow-500 text-black font-bold py-2 px-4 rounded hover:bg-yellow-600">Reset Password</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="activeModal === 'delete'" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" @click="activeModal = null"></div>
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl relative z-10 p-6 text-center border-t-8 border-red-600">
            <h2 class="text-xl font-bold mb-2 text-gray-800">Hapus Akun Krani?</h2>
            <p class="text-gray-600 mb-6">Anda akan menghapus hak akses untuk Krani: <span x-text="deleteName" class="font-extrabold text-black"></span>. Data surat yang dia input tidak akan hilang.</p>
            
            <form method="POST" :action="'{{ url('users') }}/' + deleteId">
                @csrf
                @method('DELETE')
                <div class="flex justify-center space-x-3">
                    <button type="button" @click="activeModal = null" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded">Batal</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" class="fixed top-5 right-5 z-50 bg-red-600 text-white px-6 py-4 rounded shadow-2xl border-l-4 border-red-900">
            <button @click="show = false" class="absolute top-1 right-2 text-white font-bold">&times;</button>
            <p class="font-extrabold mb-1">Pendaftaran Gagal:</p>
            <ul class="list-disc pl-4 text-sm font-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection