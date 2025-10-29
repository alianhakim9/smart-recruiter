<?php

namespace App\Livewire;

use App\Models\Resume;
use App\Services\ResumeParserService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ResumeManager extends Component
{
    use WithFileUploads;

    public $resumeFile;
    public $title;

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'resumeFile' => 'required|file|mimes:pdf|max:5120', // 5MB limit
    ];

    public function mount()
    {
        // Panggil ini untuk me-load daftar CV saat komponen dimuat
    }

    public function saveResume(ResumeParserService $parserService)
    {
        // 1. Validasi
        $this->validate();
        // 2. Parsing Teks dari File
        $parsedText = $parserService->parse($this->resumeFile);
        if (is_null($parsedText)) {
            // Tampilkan error jika parsing gagal
            session()->flash('error', 'Gagal memproses file. Pastikan format PDF valid.');
            return;
        }
        // 3. Simpan File ke Storage Laravel
        // Simpan file ke disk 'public' di folder 'resumes'
        $filePath = $this->resumeFile->store('resumes', 'public');
        // 4. Simpan Data ke Database
        auth()->user()->resume()->create(
            [
                'title' => $this->title,
                'file_path' => $filePath,
                'parsed_text' => $parsedText,
            ]
        );

        // 5. Reset dan Notifikasi
        $this->reset(['resumeFile', 'title']);
        session()->flash('message', 'CV berhasil diunggah dan siap dianalisis!');
    }

    public function deleteResume(Resume $resume)
    {
        // Otorisasi: Pastikan user hanya menghapus CV miliknya
        if (auth()->id() !== $resume->user_id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Hapus file dari storage
        Storage::disk('public')->delete($resume->file_path);

        // Hapus record dari database
        $resume->delete();

        session()->flash('message', 'CV berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.resume-manager', [
            'resumes' => auth()->user()->resume()->latest()->get(),
        ]);
    }
}
