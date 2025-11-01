<div wire:poll.5000ms>
    @if (session()->has('message'))
        <div class="p-3 bg-blue-200 text-blue-800 rounded mb-4">{{ session('message') }}</div>
    @endif

    @forelse ($reports as $report)
        <div
            class="p-4 border rounded shadow-sm mb-3 flex justify-between items-center {{ $report->status === 'completed' ? 'bg-green-50 border-green-200' : ($report->status === 'failed' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }}">

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate">
                    <span class="text-indigo-600">{{ $report->resume->title ?? 'N/A' }}</span>
                    vs
                    <span class="text-purple-600">{{ $report->jobDescription->title ?? 'N/A' }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Dimulai: {{ $report->created_at->diffForHumans() }}
                </p>
            </div>

            <div class="text-right ml-4 flex-shrink-0">
                @if ($report->status === 'completed')
                    <span
                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        ✅ {{ $report->match_score }}% Match
                    </span>
                @elseif ($report->status === 'processing')
                    <span
                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        ⏳ Processing...
                    </span>
                @elseif ($report->status === 'failed')
                    <span
                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        ❌ Failed
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Queued
                    </span>
                @endif

                @if ($report->status === 'completed')
                    {{-- <button wire:click="showReportDetails('{{ $report->id }}')"
                        class="ml-2 text-indigo-500 hover:text-indigo-700 text-sm font-medium">
                        Lihat Detail
                    </button> --}}
                    <a href="{{ route('reports.show', $report->id) }}"
                        class="ml-2 text-indigo-500 hover:text-indigo-700 text-sm font-medium">
                        Lihat Detail
                    </a>
                @endif
            </div>
        </div>
    @empty
        <p class="p-4 text-gray-500 border rounded">Tidak ada riwayat analisis.</p>
    @endforelse
</div>
