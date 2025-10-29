<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2 text-indigo-700">Manajer CV (Sumber Data 1)</h3>
                    <livewire:resume-manager />
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2 text-purple-700">Manajer lowongan (Sumber data 2)
                    </h3>
                    <livewire:job-manager />
                </div>
            </div>

        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4 border-b pb-2 text-green-700">Analisis Kecocokan AI</h3>
                <livewire:analysis-creator />
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4 border-b pb-2 text-blue-700">Riwayat Laporan Analisis</h3>
                <livewire:analysis-report-list />
            </div>
        </div>
    </div>
</x-app-layout>
