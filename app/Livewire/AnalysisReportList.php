<?php

namespace App\Livewire;

use Livewire\Component;

class AnalysisReportList extends Component
{
    protected $listeners = [
        'analysisStarted' => 'render'
    ];

    public function getReportsProperty()
    {
        return auth()
            ->user()
            ->analysisReports()
            ->with([
                'resume:id,title',
                'jobDescription:id,title'
            ])
            ->latest()
            ->get();
    }

    public function showReportDetails($reportId)
    {
        session()->flash('message', 'Fitur detail laporan (ID: ' . $reportId . ') akan diimplementasikan di Minggu 4.');
    }
    public function render()
    {
        return view('livewire.analysis-report-list', [
            'reports' => $this->reports
        ]);
    }
}
