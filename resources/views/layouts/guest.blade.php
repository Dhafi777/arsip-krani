<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Portal Login - Arsip Krani</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 via-green-100 to-green-300">
            
            <div class="mb-4">
                <img src="{{ asset('images/logo-ptpn4.png') }}" alt="Logo PTPN IV" class="w-32 h-auto drop-shadow-lg">
            </div>

            <div class="w-full sm:max-w-md px-10 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-xl border-t-8 border-green-700 relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 to-blue-500"></div>
                
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-xs font-bold text-green-800 tracking-widest">
                &copy; {{ date('Y') }} SEKRETARIAT PERUSAHAAN PTPN IV REGIONAL III
            </div>
        </div>
    </body>
</html>