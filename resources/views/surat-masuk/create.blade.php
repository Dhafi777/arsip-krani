<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Surat Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <strong class="font-bold">Oops! Ada kesalahan.</strong>
                            <ul class="list-disc mt-2 ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. Agenda</label>
                                <input type="text" name="no_agenda" value="{{ old('no_agenda') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. Surat</label>
                                <input type="text" name="no_surat" value="{{ old('no_surat') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Surat</label>
                                <input type="date" name="tgl_surat" value="{{ old('tgl_surat') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                                <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Surat</label>
                                <select name="jenis_surat" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="internal" {{ old('jenis_surat') == 'internal' ? 'selected' : '' }}>Internal</option>
                                    <option value="eksternal" {{ old('jenis_surat') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pengirim</label>
                                <input type="text" name="pengirim" value="{{ old('pengirim') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Perihal</label>
                            <textarea name="perihal" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('perihal') }}</textarea>
                        </div>

                        <div class="mb-6 border border-dashed border-gray-400 p-4 rounded-md bg-gray-50">
                            <label class="block text-sm font-medium text-gray-700">Upload Softfile Surat (PDF) - <span class="text-gray-500 italic">Opsional</span></label>
                            <input type="file" name="file_surat" accept=".pdf" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">Maksimal ukuran file: 5MB.</p>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('surat-masuk.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">Batal</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded text-sm">Simpan Surat</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>