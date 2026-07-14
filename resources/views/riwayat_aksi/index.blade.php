@extends('layouts.master')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 h-full flex flex-col border-t-4 border-gray-700">
    <div class="flex justify-between items-center mb-4 border-b-2 border-gray-200 pb-3">
        <h2 class="text-2xl font-bold text-gray-800">
            <svg class="w-6 h-6 inline-block mr-2 text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat Aksi Sistem
        </h2>
    </div>

    <div class="overflow-x-auto flex-1">
        <table class="w-full whitespace-nowrap">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                    <th class="py-3 px-4 text-left w-12">No</th>
                    <th class="py-3 px-4 text-left w-48">Waktu Kejadian</th>
                    <th class="py-3 px-4 text-left w-40">Aktor</th>
                    <th class="py-3 px-4 text-center w-40">Tipe Aksi</th>
                    <th class="py-3 px-4 text-left">Deskripsi Detail</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($logs as $index => $log)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $logs->firstItem() + $index }}</td>
                    
                    <td class="py-3 px-4 font-semibold text-gray-700">
                        {{ \Carbon\Carbon::parse($log->created_at)->locale('id')->translatedFormat('l, d M Y - H:i') }}
                    </td>
                    
                    <td class="py-3 px-4 font-bold text-gray-900">
                        {{ $log->user->name ?? 'User Dihapus' }}
                    </td>
                    
                    <td class="py-3 px-4 text-center">
                        @php
                            $badgeColor = match($log->action) {
                                'Tambah Surat Masuk', 'Tambah Krani' => 'bg-green-100 text-green-700 border border-green-300',
                                'Edit Surat Masuk', 'Update Disposisi', 'Reset Password' => 'bg-blue-100 text-blue-700 border border-blue-300',
                                'Hapus Surat Masuk', 'Hapus Krani' => 'bg-red-100 text-red-700 border border-red-300',
                                'Catatan Admin' => 'bg-yellow-100 text-yellow-700 border border-yellow-300',
                                default => 'bg-gray-100 text-gray-700 border border-gray-300'
                            };
                        @endphp
                        <span class="{{ $badgeColor }} py-1 px-3 rounded text-xs font-bold">{{ $log->action }}</span>
                    </td>
                    
                    <td class="py-3 px-4 text-gray-700">
                        {{ $log->description }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 px-4 text-center text-gray-500 font-bold">
                        Belum ada riwayat aksi yang terekam di sistem.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection