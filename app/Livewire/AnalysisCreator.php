<?php

namespace App\Livewire;

use App\Jobs\ProcessAnalysisReport;
use Livewire\Component;

class AnalysisCreator extends Component
{

    public $selectedResumeId;
    public $selectedJobId;

    public $resumes = [];
    public $jobs = [];


    protected $rules = [
        // KRITIS: Gunakan NAMA TABEL database yang benar (plural)
        'selectedResumeId' => 'required|uuid|exists:resumes,id', // <-- Ubah 'resumes' jika nama tabel Anda berbeda
        'selectedJobId' => 'required|uuid|exists:job_descriptions,id', // <-- Ubah 'job_descriptions' jika nama tabel Anda berbeda
    ];

    public function mount()
    {
        $this->loadData();
    }

    private function loadData()
    {
        $user = auth()->user();
        $this->resumes = $user->resume()->select('id', 'title')->get();
        $this->jobs = $user->jobDescription()->select('id', 'title')->get();
    }


    public function startAnalysis()
    {
        $this->validate();

        try {
            $report = auth()->user()->analysisReports()->create([
                'resume_id' => $this->selectedResumeId,
                'job_description_id' => $this->selectedJobId,
                'status' => 'pending'
            ]);

            ProcessAnalysisReport::dispatch($report);

            $this->reset(['selectedResumeId', 'selectedJobId']);

            session()->flash('analysis_success', '🚀 Analisis telah dimulai! Mohon tunggu beberapa saat hingga AI selesai memproses.');

            $this->dispatch('analysisStarted');
        } catch (\Throwable $th) {
            session()->flash('analysis_error', 'Gagal memulai analisis: ' . $th->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.analysis-creator');
    }
}
