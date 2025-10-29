<?php

namespace App\Livewire;

use App\Models\JobDescription;
use Livewire\Component;

class JobManager extends Component
{
    public $title = '';
    public $originalText = '';

    protected $rules = [
        'title' => 'required|string|min:5|max:255',
        'originalText' => 'required|string|min:50'
    ];

    public function saveJob()
    {
        $this->validate();

        auth()->user()->jobDescription()->create([
            'title' => $this->title,
            'original_text' => $this->originalText
        ]);

        $this->reset([
            'title',
            'originalText'
        ]);

        session()->flash('job_message', 'Deskripsi Lowongan berasil disimpan!');
    }

    public function deleteJob(JobDescription $job)
    {
        if (auth()->id() !== $job->user_id) {
            abort(403, 'Aksi tidak diizinkan');
        }

        $job->delete();

        session()->flash('job_message', 'Lowongan berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.job-manager', [
            'jobs' => auth()->user()->jobDescription()->latest()->get(),
        ]);
    }
}
