<?php

namespace App\Http\Controllers;

use App\Models\AnalysisReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalysisReportController extends Controller
{
    public function show(AnalysisReport $report)
    {
        $report->load([
            'resume',
            'jobDescription'
        ]);

        if ($report->status !== 'completed' || is_null($report->ai_feedback)) {
            Log::warning('Akses detail untuk laporan belum selesai atau data NULL. ID : ' . $report->id);
            return redirect()->route('dashboard')->with('error', 'Laporan belum selesai diproses atau data tidak tersedia.');
        }

        $aiFeedback = $report->ai_feedback;

        if (!isset($aiFeedback['match_score']) || !isset($aiFeedback['summary'])) {
            return redirect()->route('dashboard')->with('error', 'Struktur feedback AI rusak. Hubungi Administrator');
        }

        return view('reports.show', [
            'report' => $report,
            'aiFeedback' => $aiFeedback
        ]);
    }
}
