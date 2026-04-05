<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0f766e">
    <title>E-Antrian Kunjungan Lapas Sumbawa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Prevent horizontal scroll on mobile */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        /* Improve touch targets */
        @media (max-width: 640px) {
            button, a, input, select, textarea {
                min-height: 44px;
                min-width: 44px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <!-- Header - Mobile Optimized -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-teal-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-sm sm:text-lg font-bold text-gray-900 truncate">E-Antrian Lapas Sumbawa</h1>
                        <p class="text-[10px] sm:text-xs text-gray-500 hidden sm:block">Sistem Antrian Kunjungan Online</p>
                    </div>
                </div>
                <a href="{{ route('admin.login') }}" class="text-xs sm:text-sm font-medium text-teal-700 hover:text-teal-800 px-2 py-1">
                    Login
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content - Mobile First -->
    <main class="flex-grow flex items-start sm:items-center justify-center py-4 sm:py-8 lg:py-12 px-3 sm:px-4 lg:px-8">
        <div class="w-full max-w-md lg:max-w-lg space-y-4 sm:space-y-6">
            
            <!-- Current Queue Display - Mobile Optimized -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-teal-100 p-4 sm:p-6 lg:p-8 text-center">
                <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide mb-1 sm:mb-2">Antrian Saat Ini</p>
                <div class="text-5xl sm:text-6xl lg:text-7xl font-black text-teal-700 mb-1 sm:mb-2 leading-tight">{{ $currentQueueNumber }}</div>
                <p class="text-xs sm:text-sm text-gray-500">Sesi {{ $currentSession }}</p>
            </div>

            <!-- Take Queue Button - Large Touch Target -->
            <div class="text-center px-1 sm:px-0">
                <a href="{{ route('ambil-antrian') }}" 
                   class="inline-flex items-center justify-center w-full px-4 sm:px-8 py-4 sm:py-5 border border-transparent text-base sm:text-lg font-bold rounded-xl text-white bg-teal-700 hover:bg-teal-800 transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-95">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="truncate">Ambil Antrian</span>
                </a>
                <p class="mt-2 sm:mt-3 text-xs sm:text-sm text-gray-500 px-2">Klik untuk mendaftar antrian kunjungan</p>
            </div>

            <!-- Service Hours - Mobile Optimized -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="truncate">Jam Pelayanan</span>
                </h3>
                <div class="space-y-2 sm:space-y-3">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-2 sm:p-3 bg-orange-50 rounded-lg border border-orange-100 gap-1 sm:gap-0">
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-orange-400 rounded-full mr-2 sm:mr-3 flex-shrink-0"></span>
                            <span class="text-sm font-medium text-gray-700">Sesi Pagi</span>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-orange-700 pl-4 sm:pl-0">{{ $serviceHours['pagi'][0] }} - {{ $serviceHours['pagi'][1] }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-2 sm:p-3 bg-blue-50 rounded-lg border border-blue-100 gap-1 sm:gap-0">
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-blue-400 rounded-full mr-2 sm:mr-3 flex-shrink-0"></span>
                            <span class="text-sm font-medium text-gray-700">Sesi Siang</span>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-blue-700 pl-4 sm:pl-0">{{ $serviceHours['siang'][0] }} - {{ $serviceHours['siang'][1] }}</span>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer - Mobile Optimized -->
    <footer class="bg-gray-800 text-white py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <div class="text-center">
                <p class="text-xs sm:text-sm">&copy; {{ date('Y') }} Lapas Kelas IIB Sumbawa.</p>
                <p class="text-[10px] sm:text-xs text-gray-400 mt-1">Sistem Antrian Online</p>
            </div>
        </div>
    </footer>
</body>
</html>
