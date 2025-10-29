<div>
    @if (session()->has('analysis_success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">{{ session('analysis_success') }}</div>
    @endif
    @if (session()->has('analysis_error'))
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">{{ session('analysis_error') }}</div>
    @endif

    <form wire:submit.prevent="startAnalysis" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="resume_id" class="block text-sm font-medium text-gray-700">Pilih CV (Sumber 1)</label>
                <select id="resume_id" wire:model="selectedResumeId"
                    class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">-- Pilih CV Anda --</option>
                    @foreach ($resumes as $resume)
                        <option value="{{ $resume->id }}">{{ $resume->title }}</option>
                    @endforeach
                </select>
                @error('selectedResumeId')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="job_id" class="block text-sm font-medium text-gray-700">Pilih Lowongan (Sumber 2)</label>
                <select id="job_id" wire:model="selectedJobId"
                    class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">-- Pilih Lowongan Target --</option>
                    @foreach ($jobs as $job)
                        <option value="{{ $job->id }}">{{ $job->title }}</option>
                    @endforeach
                </select>
                @error('selectedJobId')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <button type="submit"
            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Mulai Analisis Kecocokan AI
        </button>
    </form>
</div>
