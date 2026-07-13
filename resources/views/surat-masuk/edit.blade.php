<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Surat Masuk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">

    <div class="bg-gradient-to-b from-white to-blue-100 w-full max-w-3xl rounded-lg shadow-xl overflow-hidden border border-gray-300">
        <div class="p-8">
            <h2 class="text-2xl font-extrabold text-black mb-6 border-b-2 border-blue-500 pb-2 inline-block">Edit Surat Masuk</h2>

            <form action="{{ route('surat-masuk.update', $surat->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 border border-gray-200 rounded mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500">No. Agenda (Terkunci)</label>
                        <p class="font-bold text-lg">{{ $surat->no_agenda }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500">Tanggal Masuk (Terkunci)</label>
                        <p class="font-bold text-lg">{{ \Carbon\Carbon::parse($surat->tgl_masuk)->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                        <input type="date" name="tgl_surat" value="{{ $surat->tgl_surat }}" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-1">Nomor Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="no_surat" value="{{ $surat->no_surat }}" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-black mb-1">Jenis Surat <span class="text-red-500">*</span></label>
                    <select name="jenis_surat" required class="w-1/3 border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="internal" {{ $surat->jenis_surat == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="eksternal" {{ $surat->jenis_surat == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-black mb-1">Pengirim <span class="text-red-500">*</span></label>
                    <input type="text" name="pengirim" value="{{ $surat->pengirim }}" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-black mb-1">Perihal <span class="text-red-500">*</span></label>
                    <textarea name="perihal" rows="3" required class="w-full border-black border-2 p-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $surat->perihal }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-300 mt-6">
                    <a href="{{ route('surat-masuk.index') }}" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow-md transition">Batal</a>
                    <button type="submit" class="bg-gradient-to-b from-blue-400 to-blue-600 border border-blue-800 text-white font-bold py-2 px-8 rounded shadow-md hover:opacity-90 transition">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>