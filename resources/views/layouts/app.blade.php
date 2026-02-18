<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CRM') }}</title>

    <!-- Fonts (اختیاری) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- ✅ مهم‌ترین بخش: Tailwind/Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-950 text-white">
    <div class="min-h-screen">

        {{-- ✅ Navbar --}}
        @include('layouts.navigation')

        {{-- Header (اگر صفحه‌ای header بدهد) --}}
        @isset($header)
            <header class="border-b border-white/10 bg-slate-950/50 backdrop-blur">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Main --}}
        <main class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>
</body>
</html>
