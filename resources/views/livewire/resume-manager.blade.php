<div>
    @if (session()->has('message'))
        <div class="p-3 bg-green-200 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3 bg-red-200 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent='saveResume' enctype="multipart/form-data" class="mb-6 p-4 border rounded shadow-sm">
        <h3 class="font-bold mb-3">Unggah CV Baru</h3>

        <div class="mb-4">
            <label for="title" class="block text-sm font-medium">Judul CV</label>
            <input type="text" id="title" wire:model='title' class="mt-1 block w-full border p-2 rounded">
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="resumeFile" class="block text-sm font-medium">File CV (PDF)</label>
            <input type="file" id="resumeFile" wire:model='resumeFile' class="mt-1 block w-full">
            @error('resumeFile')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"
            x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">
            <div x-show="isUploading" class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${progess}%`"></div>
            </div>
        </div>

        <button type="submit" class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">Unggah dan
            Proses</button>
    </form>

    <h3 class="font-bold mb-3 mt-8">Daftar CV anda</h3>
    <ul>
        @forelse ($resumes as $resume)
            <li class="flex justify-between items-center p-3 border-b">
                <span>{{ $resume->title }} ({{ $resume->created_at->diffForHumans() }})</span>
                <button wire:click="deleteResume('{{ $resume->id }}')" wire:confirm='Yakin ingin menghapus CV ini ?'
                    class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
            </li>
        @empty
            <li class="p-3 text-gray-500">Belum ada CV yang diunggah.</li>
        @endforelse
    </ul>

</div>
