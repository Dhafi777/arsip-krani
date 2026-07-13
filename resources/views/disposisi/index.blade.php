@extends('layouts.master')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden h-full" 
    x-data="{ 
        activeModal: null, 
        editForm: { id: '', no_surat: '', tgl_surat: '', pengirim: '', perihal: '', status_disposisi: 'By Element' },
        
        async submitEdit(formElement) {
            if (!this.editForm.id) return;
            
            let formData = new FormData(formElement);
            formData.append('_method', 'PUT'); 
            let targetUrl = `{{ url('disposisi') }}/${this.editForm.id}`;
            
            try {
                let response = await fetch(targetUrl, {
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                });
                if(response.ok) {
                    alert('Status & File Disposisi berhasil di-update!');
                    window.location.reload(); 
                } else {
                    let errorText = await response.text();
                    let errorJson = JSON.parse(errorText);
                    alert('SERVER MENOLAK: ' + (errorJson.message || 'Data tidak valid.'));
                }
            } catch(e) { alert('JS ERROR: ' + e.message); }
        }
    }"
    @open-edit.window="editForm = $event.detail; activeModal = 'edit'"
    @keydown.escape.window="activeModal = null">

    <div class="flex-1 flex flex-col p-8 bg-gray-50 overflow-hidden">
        
        <div class="mb-8 flex-shrink-0 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-black tracking-wide">DISPOSISI</h1>
                <div class="flex h-2 w-48 mt-1">
                    <div class="w-1/4 bg-blue-900"></div><div class="w-1/4 bg-blue-500"></div><div class="w-1/4 bg-yellow-400"></div><div class="w-1/4 bg-green-500"></div>
                </div>
            </div>
            
            <form action="{{ route('disposisi.index') }}" method="GET" class="flex space-x-3 items-center">
                <!-- Dropdown Filter -->
                <select name="filter" onchange="this.form.submit()" class="border-gray-300 rounded shadow-sm text-sm bg-blue-100 font-semibold focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="By Element" {{ request('filter') == 'By Element' ? 'selected' : '' }}>By Element</option>
                    <option value="Diserahkan ke Head" {{ request('filter') == 'Diserahkan ke Head' ? 'selected' : '' }}>Diserahkan ke Head</option>
                    <option value="Diteruskan ke Bagian" {{ request('filter') == 'Diteruskan ke Bagian' ? 'selected' : '' }}>Diteruskan ke Bagian</option>
                </select>

                <!-- Input Pencarian -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Surat..." class="border-gray-300 rounded shadow-sm text-sm w-48 bg-blue-100 placeholder-gray-500 font-semibold focus:ring-blue-500 pr-8">
                    <button type="submit" class="absolute right-2 top-2 text-gray-500 hover:text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>

                <!-- Tombol Reset (Hanya muncul jika sedang melakukan filter/pencarian) -->
                @if(request('filter') || request('search'))
                    <a href="{{ route('disposisi.index') }}" class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-3 rounded shadow-sm font-bold transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- TABEL -->
        <div class="bg-white rounded-lg shadow border border-gray-200 flex-1 overflow-y-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white sticky top-0 z-10 border-b-2 border-black">
                    <tr>
                        <th class="px-4 py-3 text-left">Nomor Surat</th>
                        <th class="px-4 py-3 text-left">Tgl. Masuk</th>
                        <th class="px-4 py-3 text-left">Tgl. Surat</th>
                        <th class="px-4 py-3 text-left">Pengirim</th>
                        <th class="px-4 py-3 text-left w-64">Perihal</th>
                        <th class="px-4 py-3 text-left">Status Disposisi</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($surat as $index => $s)
                    <tr class="{{ $index % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-gray-100 transition">
                        <td class="px-4 py-3 font-semibold">{{ $s->no_surat }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($s->tgl_masuk)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($s->tgl_surat)->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-bold">{{ strtoupper($s->pengirim) }}</td>
                        <td class="px-4 py-3 text-xs">{{ $s->perihal }}</td>
                        <td class="px-4 py-3">
                            @php
                                $status = $s->status_disposisi ?? 'By Element';
                                $color = match($status) { 
                                    'Diserahkan ke Head' => 'text-red-600', 
                                    'Diteruskan ke Bagian' => 'text-green-600', 
                                    default => 'text-gray-500' 
                                };
                            @endphp
                            <span class="{{ $color }} font-bold text-xs">{{ $status }}</span>
                            
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- Tombol Print Native -->
                                 @if($s->file_hasil_disposisi)
                                    <a href="{{ asset('storage/' . $s->file_hasil_disposisi) }}" target="_blank" class="text-green-600 hover:text-green-900 border border-green-400 bg-green-50 rounded p-1 transition" title="Lihat Hasil Disposisi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                @else
                                    <button type="button" disabled class="text-gray-300 border border-gray-200 bg-gray-50 rounded p-1 cursor-not-allowed" title="File belum di-upload">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                @endif
                                <a href="{{ route('disposisi.cetak', $s->id) }}" target="_blank" class="text-gray-700 hover:text-gray-900 border border-gray-400 bg-gray-100 rounded p-1" title="Cetak Disposisi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <!-- Tombol Edit (Status & Upload) -->
                                <button type="button" @click="$dispatch('open-edit', @js([ 'id' => $s->id, 'no_surat' => $s->no_surat, 'tgl_surat' => \Carbon\Carbon::parse($s->tgl_surat)->format('Y-m-d'), 'pengirim' => $s->pengirim, 'perihal' => $s->perihal, 'status_disposisi' => $s->status_disposisi ?? 'By Element' ]))" class="text-blue-600 hover:text-blue-900 border border-blue-400 bg-blue-50 rounded p-1" title="Update Status & Upload">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-6 font-bold">Data kosong.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-2">
            {{ $surat->links() }}
        </div>

        <!-- MODAL UBAH STATUS & UPLOAD -->
        <div x-show="activeModal === 'edit'" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60" @click="activeModal = null"></div>
            <div class="bg-gradient-to-b from-white to-blue-200 w-full max-w-2xl rounded-lg shadow-2xl relative z-10 p-8">
                <button type="button" @click="activeModal = null" class="absolute top-5 right-6 text-red-600 font-extrabold text-3xl">&times;</button>
                <h2 class="text-2xl font-extrabold mb-6 border-b-2 border-blue-500 pb-2 inline-block">Status Disposisi</h2>
                
                <form id="formDisposisi" @submit.prevent="submitEdit($event.target)" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-gray-500">Nomor Surat</label><input type="text" x-model="editForm.no_surat" readonly class="w-full bg-gray-200 p-2 text-sm rounded font-bold"></div>
                        <div><label class="block text-xs font-bold text-gray-500">Pengirim</label><input type="text" x-model="editForm.pengirim" readonly class="w-full bg-gray-200 p-2 text-sm rounded font-bold"></div>
                    </div>
                    <div><label class="block text-xs font-bold text-gray-500">Perihal</label><textarea x-model="editForm.perihal" readonly rows="2" class="w-full bg-gray-200 p-2 text-sm rounded font-bold"></textarea></div>
                    
                    <div class="border-t border-gray-400 pt-4 mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">Pilih Status Disposisi</label>
                            <select name="status_disposisi" x-model="editForm.status_disposisi" required class="w-full border-black border-2 p-2 text-sm bg-white font-bold text-blue-900 focus:ring-2 focus:ring-blue-500">
                                <option value="By Element">By Element (Default)</option>
                                <option value="Diserahkan ke Head">Diserahkan ke Head</option>
                                <option value="Diteruskan ke Bagian">Diteruskan ke Bagian</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">
                                Upload Hasil Disposisi (PDF)
                                <span x-show="editForm.status_disposisi === 'Diteruskan ke Bagian'" class="text-red-600">*</span>
                            </label>
                            
                            <div class="border-black border-2 bg-white p-1 rounded">
                                <input type="file" name="file_hasil_disposisi" accept=".pdf" class="w-full text-sm" 
                                    :required="editForm.status_disposisi === 'Diteruskan ke Bagian'">
                            </div>
                            
                            <p x-show="editForm.status_disposisi === 'Diteruskan ke Bagian'" class="text-xs text-red-600 font-bold mt-1">
                                * File PDF wajib dilampirkan untuk status ini.
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 mt-2">
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-8 rounded shadow-md hover:bg-blue-800 transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection