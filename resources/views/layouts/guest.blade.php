<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>{{ config('app.name', 'Simple CRM') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: #0f172a;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-page {
            width: 100%;
            max-width: 420px;
        }

        .auth-logo {
            text-align: center;
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .auth-card {
            width: 100%;
            background: #ffffff;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 15px;
            outline: none;
            margin-bottom: 14px;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        input[type="checkbox"] {
            margin-right: 6px;
        }

        button,
        input[type="submit"] {
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover,
        input[type="submit"]:hover {
            background: #1d4ed8;
        }

        a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-end {
            justify-content: flex-end;
        }

        .justify-between {
            justify-content: space-between;
        }

        .block {
            display: block;
        }

        .w-full {
            width: 100%;
        }

        .text-sm {
            font-size: 14px;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .underline {
            text-decoration: underline;
        }

        .ms-3 {
            margin-left: 12px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .auth-error,
        .text-red-600 {
            color: #dc2626;
            font-size: 13px;
            margin-top: -8px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="auth-page">
        <div class="auth-logo">
            Simple CRM
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>
</body>
</html>