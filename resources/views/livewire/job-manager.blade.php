<div>
    @if (session()->has('job_message'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">
            {{ session('job_message') }}
        </div>
    @endif

    <form wire:submit.prevent='saveJob' class="mb-6 p-4 border rounded shadow-sm">
        <h3 class="font-bold mb-3 text-lg">Input Deskripsi Lowongan Kerja</h3>

        <div class="mb-4">
            <label for="job-title" class="block text-sm font-medium">Nama lowongan (Contoh : Backend Developer)</label>
            <input type="text" id="job-title" wire:model.defer='title' class="mt-1 block w-full p-2 rounded">
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="original-text" class="block text-sm font-medium">Tempel (Copy-Paste) Teks Deskripsi
                Lowongan</label>
            <textarea id="original-text" wire:model.defer='originalText' rows="6"
                class="mt-1 block w-full border p-2 rounded"></textarea>
            @error('originalText')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="mt-2 bg-purple-600 text-white py-2 px-4 rounded hover:bg-purple-700">
            Simpan Lowongan
        </button>
    </form>

    <h3 class="font-bold mb-3 mt-8 text-lg">Lowongan Tersimpan ({{ $jobs->count() }})</h3>
    <ul class="space-y-2">
        @forelse ($jobs as $job)
            <li class="flex justify-between items-start p-3 border rounded bg-white">
                <div>
                    <span class="font-semibold">{{ $job->title }}</span>
                    <p class="text-xs text-gray-500 mt-1">Dibuat : {{ $job->created_at->diffForHumans() }}</p>
                </div>

                <button wire:click="deleteJob('{{ $job->id }}')" wire:confirm='Hapus lowongan ini?'
                    class="text-red-600 hover:text-red-800 text-sm ml-4 flex-shrink-0">Hapus</button>
            </li>
        @empty
            <li class="p-3 text-gray-500 border rounded bg-white">Belum ada lowongan yang disimpan.</li>
        @endforelse
    </ul>
</div>
