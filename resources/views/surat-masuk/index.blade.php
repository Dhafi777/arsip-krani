@extends('layouts.master')

@section('content')
@if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-8"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-init="setTimeout(() => show = false, 3000)" 
         class="fixed top-5 right-5 z-50 bg-green-600 text-white px-6 py-4 rounded shadow-2xl flex items-center gap-3 border-l-4 border-green-900">
        
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        
        <span class="font-bold text-sm">{{ session('success') }}</span>
        
        <button @click="show = false" class="text-white hover:text-gray-200 ml-4 font-bold text-xl">&times;</button>
    </div>
@endif

<div class="flex-1 flex flex-col overflow-hidden h-full" 
    x-data="{ 
        activeModal: '{{ $errors->any() ? 'tambah' : '' }}',
        editForm: { id: '', no_agenda: '', tgl_masuk: '', tgl_surat: '', no_surat: '', jenis_surat: '', pengirim: '', perihal: '' },
        deleteId: '',
        deleteNoSurat: '',
        catatanForm: { id: '', catatan: '' },
        
        /* DATA UNTUK MODAL DETAIL ARSIP */
        detailData: { no_surat: '', pengirim: '', file_surat: null, file_hasil_disposisi: null },
        
        async submitEdit() {
            if (!this.editForm.id) return;
            
            let formElement = document.getElementById('formEditSurat');
            let formData = new FormData(formElement);
            formData.append('_method', 'PUT');
            
            let targetUrl = `{{ url('surat-masuk') }}/${this.editForm.id}`;
            
            try {
                let response = await fetch(targetUrl, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Accept': 'application/json' 
                    },
                    body: formData
                });
                
                if(response.ok) {
                    alert('Data arsip dan file berhasil di-update!');
                    window.location.reload(); 
                } else {
                    let errorText = await response.text();
                    try {
                        let errorJson = JSON.parse(errorText);
                        alert('SERVER MENOLAK: ' + (errorJson.message || 'Data tidak valid.'));
                    } catch (err) {
                        alert('CRITICAL ERROR!');
                    }
                }
            } catch(e) { alert('JS ERROR: ' + e.message); }
        },

        async submitCatatan(formElement) {
            if (!this.catatanForm.id) return;
            
            let formData = new FormData(formElement);
            formData.append('_method', 'PUT'); 
            
            let targetUrl = `{{ url('surat-masuk') }}/${this.catatanForm.id}/catatan`;
            
            try {
                let response = await fetch(targetUrl, {
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Accept': 'application/json' 
                    },
                    body: formData
                });
                
                if(response.ok) {
                    alert('Catatan berhasil dikirim ke Krani!');
                    window.location.reload(); 
                } else {
                    let errorText = await response.text();
                    let errorJson = JSON.parse(errorText);
                    alert('SERVER MENOLAK: ' + (errorJson.message || 'Terjadi kesalahan.'));
                }
            } catch(e) { 
                alert('JS ERROR: ' + e.message); 
            }
        }
    }"
    @open-edit.window="editForm = $event.detail; activeModal = 'edit'"
    @open-delete.window="deleteId = $event.detail.id; deleteNoSurat = $event.detail.no_surat; activeModal = 'delete'"
    @open-catatan.window="catatanForm = $event.detail; activeModal = 'catatan'"
    @open-detail.window="detailData = $event.detail; activeModal = 'detail'"
    @keydown.escape.window="activeModal = null">

    <div class="flex-1 flex flex-col p-8 bg-gray-50 overflow-hidden">
        
        <div class="mb-8 flex-shrink-0">
            <h1 class="text-2xl font-extrabold text-black tracking-wide">ARSIP SURAT MASUK DIREKSI</h1>
            <div class="flex h-2 w-72 mt-1">
                <div class="w-1/4 bg-blue-900"></div><div class="w-1/4 bg-blue-500"></div><div class="w-1/4 bg-yellow-400"></div><div class="w-1/4 bg-green-500"></div>
            </div>
        </div>

        <form action="{{ route('surat-masuk.index') }}" method="GET" class="flex justify-end items-center mb-4 space-x-3 flex-shrink-0">
            <select name="filter_jenis" onchange="this.form.submit()" class="border-gray-300 rounded shadow-sm text-sm font-semibold bg-gray-200 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Filter</option>
                <option value="internal" {{ request('filter_jenis') == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="eksternal" {{ request('filter_jenis') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
            </select>
            <select name="sort_by" onchange="this.form.submit()" class="border-gray-300 rounded shadow-sm text-sm font-semibold bg-gray-200 focus:ring-blue-500 focus:border-blue-500">
                <option value="terbaru" {{ request('sort_by') == 'terbaru' ? 'selected' : '' }}>Input Terbaru</option>
                <option value="terlama" {{ request('sort_by') == 'terlama' ? 'selected' : '' }}>Input Terlama</option>
                <option value="tgl_surat_baru" {{ request('sort_by') == 'tgl_surat_baru' ? 'selected' : '' }}>Tgl. Surat Terbaru</option>
                <option value="tgl_surat_lama" {{ request('sort_by') == 'tgl_surat_lama' ? 'selected' : '' }}>Tgl. Surat Terlama</option>
            </select>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Surat/Pengirim..." class="border-gray-300 rounded shadow-sm text-sm w-64 bg-blue-100 placeholder-gray-500 focus:ring-blue-500 pr-8">
                <button type="submit" class="absolute right-2 top-2 text-gray-500 hover:text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </div>
            @if(auth()->user()->role === 'krani')
            <button type="button" @click="activeModal = 'tambah'" class="bg-gradient-to-b from-gray-200 to-gray-400 border border-gray-400 text-black font-bold py-2 px-4 rounded shadow-md text-sm flex items-center hover:opacity-90 transition">
                <span class="mr-1 text-lg leading-none">+</span> Tambah Surat Baru
            </button>
            @endif
        </form>

        <div class="bg-white rounded-lg shadow border border-gray-200 flex-1 overflow-y-auto">
            <table class="min-w-full text-sm text-left align-top">
               <thead class="bg-gray-200 sticky top-0 z-10 border-b-2 border-black text-black">
                <tr>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Nomor Surat</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Tgl. Masuk</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Tgl. Surat</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Pengirim</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Perihal</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Jenis Surat</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Tujuan Surat</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">File Surat</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Status</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold">Catatan Admin</th>
                    <th class="px-4 py-3 uppercase tracking-wide font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($surat as $index => $s)
                    <tr class="{{ $index % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-gray-100 transition">
                        <td class="px-4 py-3 align-top font-bold text-gray-800"> {{ $s->no_surat }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($s->tgl_masuk)->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($s->tgl_surat)->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3 align-top font-semibold text-gray-900 max-w-[200px] break-words">
                            {{ $s->pengirim }}
                        </td>
                        <td class="px-4 py-3 align-top text-gray-800">
                            {{ $s->perihal }}
                        </td>
                        <td class="px-4 py-3">{{ ucfirst($s->jenis_surat) }}</td>
                        <td class="px-4 py-3">
                            @if($s->disposition)
                                @php
                                    $color = match($s->disposition->tujuan_head) { 'RHE' => 'bg-blue-800', 'OH' => 'bg-red-800', 'BSH' => 'bg-purple-800', default => 'bg-gray-600' };
                                    $text = match($s->disposition->tujuan_head) { 'RHE' => 'REGION HEAD', 'OH' => 'OPERATION HEAD', 'BSH' => 'BUSINESS SUPPORT HEAD', default => '-' };
                                @endphp
                                <span class="{{ $color }} text-white text-xs font-bold px-3 py-1 rounded-full uppercase inline-block whitespace-nowrap text-center">{{ $text }}</span>
                            @else
                                <span class="text-gray-400 italic">Belum diset</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($s->file_surat)
                                <a href="{{ asset('storage/' . $s->file_surat) }}" target="_blank" class="inline-block hover:bg-blue-200 font-bold text-xs py-1 px-2 rounded shadow-sm">
                                   Lihat Surat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs italic">Tanpa File</span>
                            @endif
                        </td>
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
                        <td class="px-4 py-3 text-xs font-bold text-blue-600">
                         {{ $s->catatan_admin ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
            
                                @if(auth()->user()->role === 'krani')
                                    <button type="button" 
                                    @click="$dispatch('open-edit', @js([ 
                                        'id' => $s->id, 
                                        'no_agenda' => $s->no_agenda, 
                                        'tgl_masuk' => \Carbon\Carbon::parse($s->tgl_masuk)->format('Y-m-d'), 
                                        'tgl_surat' => \Carbon\Carbon::parse($s->tgl_surat)->format('Y-m-d'), 
                                        'no_surat' => $s->no_surat, 
                                        'jenis_surat' => $s->jenis_surat,
                                        'pengirim' => $s->pengirim, 
                                        'perihal' => $s->perihal, 
                                        'status_disposisi' => $s->status_disposisi ?? 'By Element' 
                                    ]))" 
                                    class="text-blue-600 hover:text-blue-900 border border-blue-400 bg-blue-50 rounded p-1 transition" title="Edit Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <!-- KODE TOMBOL HAPUS YANG BENAR -->
                                    <button type="button" @click="$dispatch('open-delete', { id: {{ $s->id }}, no_surat: '{{ $s->no_surat }}' })" class="text-red-600 hover:text-red-900 border border-red-400 bg-red-50 rounded p-1 transition" title="Hapus Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    
                                
                                @elseif(auth()->user()->role === 'admin')
                                    <button type="button" @click="$dispatch('open-catatan', @js(['id' => $s->id, 'catatan' => $s->catatan_admin]))" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-1 px-3 rounded shadow transition flex items-center whitespace-nowrap" title="Beri Catatan">
                                        Beri Catatan
                                    </button>
                                    
                                    <button type="button" @click="$dispatch('open-detail', @js([
                                        'no_surat' => $s->no_surat,
                                        'pengirim' => $s->pengirim,
                                        'file_surat' => $s->file_surat ? asset('storage/' . $s->file_surat) : null,
                                        'file_hasil_disposisi' => $s->file_hasil_disposisi ? asset('storage/' . $s->file_hasil_disposisi) : null
                                    ]))" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded shadow transition flex items-center whitespace-nowrap" title="Lihat Detail File">
                                        Detail File
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center py-6 text-gray-500 font-bold">Data masih kosong.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-2">
            {{ $surat->links() }}
        </div>
    </div>

    <div x-show="activeModal === 'tambah'" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="activeModal = null"></div>
        <div class="bg-gradient-to-b from-white to-blue-200 w-full max-w-3xl rounded-lg shadow-2xl relative max-h-[90vh] flex flex-col z-10">
            <button type="button" @click="activeModal = null" class="absolute top-5 right-6 text-red-600 hover:text-red-800 font-extrabold text-3xl leading-none z-20">&times;</button>
            <div class="p-8 overflow-y-auto">
                <h2 class="text-2xl font-extrabold text-black mb-6 border-b-2 border-blue-500 pb-2 inline-block">Tambah Surat Baru</h2>
                <h2 class="text-2xl font-extrabold text-black mb-6 border-b-2 border-blue-500 pb-2 inline-block">Tambah Surat Baru</h2>
                
                <!-- BLOK PENAMPIL ERROR -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border-l-4 border-red-600 text-red-800 p-4 rounded shadow-sm">
                        <strong class="font-extrabold">Gagal Menyimpan Data!</strong>
                        <ul class="list-disc pl-5 text-sm mt-2 font-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">No. Agenda <span class="text-red-500">*</span></label>
                            <input type="text" name="no_agenda" value="{{ $nextAgenda }}" readonly required class="w-full border-black border-2 p-2 text-sm bg-gray-200 text-gray-700 cursor-not-allowed focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">Tanggal Masuk <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_masuk" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" readonly required class="w-full border-black border-2 p-2 text-sm bg-gray-200 text-gray-700 cursor-not-allowed focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_surat" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">Nomor Surat <span class="text-red-500">*</span></label>
                            <input type="text" name="no_surat" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan Nomor Surat...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Jenis Surat</label>
                        <select name="jenis_surat" required class="w-1/3 border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Jenis Surat...</option>
                            <option value="internal">Internal</option>
                            <option value="eksternal">Eksternal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Pengirim</label>
                        <input type="text" name="pengirim" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan Nama Instansi Pengirim...">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Perihal</label>
                        <textarea name="perihal" rows="2" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan Perihal..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">Pilih Tujuan</label>
                            <select name="tujuan_head" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">RH, OH, BSH</option>
                                <option value="RHE">Region Head (RH)</option>
                                <option value="OH">Operation Head (OH)</option>
                                <option value="BSH">Business Support Head (BSH)</option>
                            </select>
                        </div>
                        <div>
                        <label class="block text-sm font-bold text-black mb-1">Upload File Arsip (PDF)</label>
                        <input type="file" name="file_surat" accept=".pdf, .PDF, application/pdf" class="w-full border-black border-2 p-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">                        </div>
                        <div class="text-right pb-1">
                            <button type="submit" class="bg-gradient-to-b from-blue-400 to-blue-600 border border-blue-800 text-white font-bold py-2 px-8 rounded shadow-md hover:opacity-90">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="activeModal === 'edit'" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="activeModal = null"></div>
        <div class="bg-gradient-to-b from-white to-blue-200 w-full max-w-3xl rounded-lg shadow-2xl relative max-h-[90vh] flex flex-col z-10">
            <button type="button" @click="activeModal = null" class="absolute top-5 right-6 text-red-600 hover:text-red-800 font-extrabold text-3xl leading-none z-20">&times;</button>
            <div class="p-8 overflow-y-auto">
                <h2 class="text-2xl font-extrabold text-black mb-6 border-b-2 border-blue-500 pb-2 inline-block">Edit Surat Masuk</h2>
                <form id="formEditSurat" @submit.prevent="submitEdit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 border border-gray-200 rounded mb-4 shadow-sm">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">No. Agenda</label>
                            <input type="text" x-model="editForm.no_agenda" readonly class="w-full bg-transparent font-bold text-lg border-none p-0 focus:ring-0 cursor-not-allowed text-gray-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Masuk</label>
                            <input type="date" x-model="editForm.tgl_masuk" readonly class="w-full bg-transparent font-bold text-lg border-none p-0 focus:ring-0 cursor-not-allowed text-gray-800">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_surat" x-model="editForm.tgl_surat" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-black mb-1">Nomor Surat <span class="text-red-500">*</span></label>
                            <input type="text" name="no_surat" x-model="editForm.no_surat" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Jenis Surat <span class="text-red-500">*</span></label>
                        <select name="jenis_surat" x-model="editForm.jenis_surat" required class="w-1/3 border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="internal">Internal</option>
                            <option value="eksternal">Eksternal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Pengirim <span class="text-red-500">*</span></label>
                        <input type="text" name="pengirim" x-model="editForm.pengirim" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Perihal <span class="text-red-500">*</span></label>
                        <textarea name="perihal" x-model="editForm.perihal" rows="3" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Update File Arsip (PDF)</label>
                    <input type="file" name="file_surat" accept=".pdf, .PDF, application/pdf" class="w-full border-black border-2 p-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">                        <p class="text-xs text-red-500 mt-1 font-semibold">*Biarkan kosong jika tidak ingin mengubah file saat ini.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-300 mt-6">
                        <button type="button" @click="activeModal = null" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow-md transition">Batal</button>
                        <button type="submit" class="bg-gradient-to-b from-blue-400 to-blue-600 border border-blue-800 text-white font-bold py-2 px-8 rounded shadow-md hover:opacity-90 transition">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="activeModal === 'delete'" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" @click="activeModal = null"></div>
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl relative z-10 p-6 text-center border-t-8 border-red-600">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <h2 class="text-xl font-bold mb-2 text-gray-800">Hapus Surat Masuk?</h2>
            <p class="text-gray-600 mb-6">Data surat dan file disposisi yang terkait akan terhapus permanen dan tidak bisa dikembalikan.</p>
            
            <form method="POST" :action="'{{ url('surat-masuk') }}/' + deleteId">
                @csrf
                @method('DELETE')
                <div class="flex justify-center space-x-3">
                    <button type="button" @click="activeModal = null" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">Batal</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <div x-show="activeModal === 'catatan'" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" @click="activeModal = null"></div>
        <div class="bg-white w-full max-w-lg rounded-lg shadow-2xl relative z-10 p-8 border-t-8 border-yellow-500">
            <button type="button" @click="activeModal = null" class="absolute top-5 right-6 text-gray-500 hover:text-red-600 font-extrabold text-2xl">&times;</button>
            <h2 class="text-xl font-extrabold mb-4 text-black">Beri Catatan / Instruksi</h2>
            
            <form @submit.prevent="submitCatatan($event.target)" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Admin</label>
                    <textarea name="catatan_admin" x-model="catatanForm.catatan" rows="4" class="w-full border-gray-400 border-2 p-3 text-sm bg-gray-50 focus:ring-2 focus:ring-yellow-500 rounded" placeholder="Ketik catatan atau instruksi untuk Krani di sini..."></textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-yellow-500 text-black font-bold py-2 px-6 rounded shadow hover:bg-yellow-600 transition">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
    
    <div x-show="activeModal === 'detail'" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="activeModal = null"></div>
        <div class="bg-white w-full max-w-xl rounded-lg shadow-2xl relative z-10 p-8 border-t-8 border-blue-600">
            <button type="button" @click="activeModal = null" class="absolute top-4 right-5 text-gray-500 hover:text-red-600 font-extrabold text-2xl">&times;</button>
            
            <h2 class="text-xl font-extrabold mb-1 text-black">Detail File Arsip</h2>
            <p class="text-sm font-bold text-blue-700 mb-6" x-text="detailData.no_surat + ' - ' + detailData.pengirim"></p>
            
            <div class="space-y-4">
                <div class="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 flex justify-between items-center hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-200 p-2 rounded">
                            <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-gray-800 text-sm">Surat Masuk Fisik</p>
                            <p class="text-xs font-bold text-red-500 mt-1" x-show="!detailData.file_surat">Belum ada file diunggah.</p>
                        </div>
                    </div>
                    <template x-if="detailData.file_surat">
                        <a :href="detailData.file_surat" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-xs shadow-md transition">Lihat Dokumen</a>
                    </template>
                </div>

                <div class="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 flex justify-between items-center hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-200 p-2 rounded">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-gray-800 text-sm">Surat Hasil Disposisi</p>
                            <p class="text-xs font-bold text-red-500 mt-1" x-show="!detailData.file_hasil_disposisi">Belum ada file diunggah.</p>
                        </div>
                    </div>
                    <template x-if="detailData.file_hasil_disposisi">
                        <a :href="detailData.file_hasil_disposisi" target="_blank" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-xs shadow-md transition">Lihat Dokumen</a>
                    </template>
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <button type="button" @click="activeModal = null" class="bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded shadow hover:bg-gray-400 transition">Tutup Panel</button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection