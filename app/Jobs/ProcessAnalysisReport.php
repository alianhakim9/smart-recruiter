<?php

namespace App\Jobs;

use App\Models\AnalysisReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessAnalysisReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $report;


    /**
     * Create a new job instance.
     */
    public function __construct(AnalysisReport $report)
    {
        $this->report = $report;

        // $this->timeout = 180;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->report->load([
            'resume',
            'jobDescription'
        ]);

        if (is_null($this->report->resume) || is_null($this->report->jobDescription)) {
            // Log ini akan bekerja jika relasi benar-benar gagal dimuat
            Log::error("DEBUG ERROR: Relasi Resume (ID: {$this->report->resume_id}) GAGAL dimuat.");
            // KARENA GAGAL, kita keluar Job secara bersih (dengan update status fail)
            $this->report->update(['status' => 'failed']);
            return;
        }

        $this->report->update([
            'status' => 'processing'
        ]);

        $resumeText = $this->report->resume->parsed_text;

        $jobText = $this->report->jobDescription->original_text;

        try {
            $prompt = $this->generatePrompt($resumeText, $jobText);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'), // WAJIB ada API Key
                'Content-Type' => 'application/json',
            ])
                // TAMBAHKAN timeout() di sini
                ->timeout(120) // Beri waktu 45 detik untuk resolusi dan respons
                ->post('https://api.openai.com/v1/chat/completions', [ // Ganti env() URL Anda dengan URL langsung
                    'model' => 'gpt-3.5-turbo-1106',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $prompt['system_prompt'],
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt['user_prompt'],
                        ]
                    ]
                ])->throw()->json();

            $aiResult = json_decode($response['choices'][0]['message']['content'], true);

            $this->report->update([
                'match_score' => $aiResult['match_score'] ?? 0,
                'ai_feedback' => $aiResult,
                'status' => 'completed'
            ]);
        } catch (\Throwable $th) {
            $this->report->update([
                'status' => 'failed'
            ]);

            logger()->error('AI anaylisis failed : ' . $th->getMessage(), [
                'report_id' => $this->report->id
            ]);

            throw $th;
        }
    }

    private function generatePrompt(string $resumeText, string $jobText): array
    {
        $systemPrompt = "You are a professional HR recruiter specializing in ATS (Applicant Tracking System) analysis. Your task is to compare a candidate's resume against a specific job description. The output MUST be a valid JSON object.";

        $userPrompt = "Analyze the following Job Description (JD) and Resume (CV).
        
        ---
        JD: \"{$jobText}\"
        ---
        CV: \"{$resumeText}\"
        ---

        Provide a strict JSON output with the following keys:
        1. match_score (Integer, 0-100): The overall compatibility score.
        2. summary (String): A brief paragraph (max 3 sentences) explaining the score.
        3. missing_keywords (Array of String): List 3-5 keywords/skills from the JD that are severely missing from the CV.
        4. suggestions (Array of String): List 2-3 actionable advice points for the candidate to improve their CV to match this JD.";

        return [
            'system_prompt' => $systemPrompt,
            'user_prompt' => $userPrompt,
        ];
    }
}
