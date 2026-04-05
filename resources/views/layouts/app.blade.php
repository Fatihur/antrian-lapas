<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e3a5f">
    <title>@yield('title', $title ?? 'E-Antrian Kunjungan Lapas Sumbawa')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire -->
    @livewireStyles
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background: linear-gradient(to bottom right, #f0f4f8, #e2e8f0);
        }
        [x-cloak] { display: none !important; }
        
        /* Custom form styling */
        .form-input {
            @apply w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/20 focus:outline-none transition-all duration-200;
        }
        .form-input.error {
            @apply border-red-500 focus:border-red-500 focus:ring-red-200;
        }
        .form-label {
            @apply block text-sm font-semibold text-gray-700 mb-2;
        }
        .form-error {
            @apply mt-1 text-sm text-red-600 font-medium;
        }
    </style>
    
    @stack('styles')
</head>
<body class="text-gray-900 antialiased min-h-screen">
    <div id="app">
        @yield('content')
        {{ $slot ?? '' }}
    </div>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
