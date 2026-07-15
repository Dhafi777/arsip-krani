<x-guest-layout>
    <x-auth-session-status class="mb-4 font-bold text-green-600 text-center" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-black tracking-tight uppercase">Sistem Arsip Surat Masuk Direksi</h2>
        <p class="text-sm text-gray-500 font-bold mt-1">Silakan masuk menggunakan akun Anda</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block font-extrabold text-sm text-gray-700">Email atau Username</label>
            <input id="email" class="block mt-1 w-full border-gray-300 border-2 focus:border-green-600 focus:ring-green-600 rounded p-2.5 text-sm font-medium transition" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan email admin/krani..." />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block font-extrabold text-sm text-gray-700">Password</label>
            <input id="password" class="block mt-1 w-full border-gray-300 border-2 focus:border-green-600 focus:ring-green-600 rounded p-2.5 text-sm font-medium transition" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-600" name="remember">
                <span class="ms-2 text-sm text-gray-600 font-bold">Ingat Saya</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center bg-gradient-to-b from-green-500 to-green-700 border-2 border-green-800 text-white font-extrabold py-3 px-4 rounded shadow-md hover:opacity-90 transition duration-150 uppercase tracking-widest text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                Masuk Sistem
            </button>
        </div>
    </form>
</x-guest-layout>