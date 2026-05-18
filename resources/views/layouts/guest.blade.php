<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="mb-6 text-2xl font-bold">
            Simple CRM
        </div>

        <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6">
            {{ $slot }}
        </div>
    </div>
</body>
</html>