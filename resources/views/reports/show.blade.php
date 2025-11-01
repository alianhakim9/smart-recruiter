<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan Analisis - {{ $report->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fc;
        }
    </style>
</head>

<body class="p-8">

    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 hover:text-blue-800 font-medium mb-6 inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Dashboard
        </a>

        <h1 class="text-3xl font-extrabold text-gray-800 border-b pb-4 mb-6">
            Detail Laporan Analisis #{{ $report->id }}
        </h1>

        <!-- Identitas Laporan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 text-sm">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="font-semibold text-gray-600">CV:</p>
                <p class="text-gray-900">{{ $report->resume->title ?? 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="font-semibold text-gray-600">Lowongan:</p>
                <p class="text-gray-900">{{ $report->jobDescription->title ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Bagian 1: Skor Kecocokan -->
        <div
            class="bg-indigo-600 text-white rounded-xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between shadow-lg">
            <div class="text-center md:text-left">
                <h2 class="text-xl font-bold">Skor Kecocokan Keseluruhan (Match Score)</h2>
                <p class="text-indigo-200">Berdasarkan perbandingan AI terhadap CV dan Job Description.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="text-6xl font-extrabold">{{ $aiFeedback['match_score'] ?? '?' }}%</span>
            </div>
        </div>

        <!-- Bagian 2: Ringkasan -->
        <div class="mb-8 p-6 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
            <h3 class="text-xl font-bold text-blue-700 mb-3">Ringkasan Analisis (Summary)</h3>
            <p class="text-gray-700 leading-relaxed">{{ $aiFeedback['summary'] ?? 'Ringkasan tidak tersedia.' }}</p>
        </div>

        <!-- Bagian 3 & 4: Kekuatan & Kelemahan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Missing Keywords -->
            <div class="p-6 bg-red-50 border border-red-200 rounded-xl">
                <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938-12.043a9 9 0 0113.876 0l1.248 2.497-1.248 2.497-2.496-1.248L12 17.5l-2.497-1.248L7.006 17.5l-1.248-2.497 1.248-2.497-2.497 1.248z">
                        </path>
                    </svg>
                    Kata Kunci Penting yang Hilang
                </h3>
                <ul class="space-y-2 text-gray-700">
                    @forelse ($aiFeedback['missing_keywords'] ?? [] as $keyword)
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">&bull;</span> {{ $keyword }}
                        </li>
                    @empty
                        <p class="text-gray-500 italic">Tidak ada kata kunci yang hilang secara signifikan.</p>
                    @endforelse
                </ul>
            </div>

            <!-- Suggestions -->
            <div class="p-6 bg-green-50 border border-green-200 rounded-xl">
                <h3 class="text-xl font-bold text-green-700 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Saran Perbaikan CV
                </h3>
                <ul class="space-y-2 text-gray-700">
                    @forelse ($aiFeedback['suggestions'] ?? [] as $suggestion)
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">&bull;</span> {{ $suggestion }}
                        </li>
                    @empty
                        <p class="text-gray-500 italic">CV sudah sangat baik, tidak ada saran mendesak.</p>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t text-sm text-gray-500 text-center">
            Laporan dibuat pada: {{ $report->created_at->format('d M Y, H:i:s') }}
        </div>
    </div>

</body>

</html>
