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
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Eager load data
        $this->report->load(['resume', 'jobDescription']);

        // Check for missing data
        if (is_null($this->report->resume) || is_null($this->report->jobDescription)) {
            Log::error("Relasi Resume atau Job Description GAGAL dimuat untuk report ID: " . $this->report->id);
            $this->report->update(['status' => 'failed']);
            return;
        }

        $this->report->update(['status' => 'processing']);

        $resumeText = $this->report->resume->parsed_text;
        $jobText = $this->report->jobDescription->original_text;

        try {
            $prompt = $this->generatePrompt($resumeText, $jobText);

            // 1. Panggilan OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])
                ->timeout(120) // Waktu yang cukup untuk respons AI
                // PENTING: Jika koneksi masih bermasalah, tambahkan ->withOptions(['verify' => false]) 
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo-1106',
                    // KRITIS: Wajib minta format JSON
                    'response_format' => ['type' => 'json_object'],
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
                ])->throw()->json(); // throw() akan melempar exception untuk error 4xx/5xx

            $aiContent = $response['choices'][0]['message']['content'];

            // Log::info("OpenAI Raw Response Content: " . $aiContent); // Buka komentar ini untuk debugging

            // 2. KRITIS: Pembersihan Konten (Hanya ambil objek JSON murni)
            if (!preg_match('/^[\s]*(\{[\s\S]*\})[\s]*$/', $aiContent, $matches)) {
                // Mencari objek JSON yang diawali dengan { dan diakhiri dengan }
                if (!preg_match('/\{[\s\S]*\}/', $aiContent, $matches)) {
                    Log::error("Konten AI tidak mengandung objek JSON yang dikenali. Konten: " . $aiContent);
                    throw new \Exception("Respons AI tidak mengandung objek JSON.");
                }
            }

            $jsonString = $matches[0]; // Ambil hanya objek JSON murni

            // 3. Decode JSON yang sudah bersih
            $aiResult = json_decode($jsonString, true);

            // 4. Pengecekan Kegagalan JSON Decode
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log konten mentah yang gagal di-decode
                Log::error("Gagal mem-parsing JSON. Error: " . json_last_error_msg() . ". Konten mentah: " . $jsonString);
                throw new \Exception("Gagal mem-parsing JSON dari OpenAI.");
            }

            // 5. Pengecekan Integritas Data (Minimal Key)
            if (!isset($aiResult['match_score']) || !isset($aiResult['suggestions'])) {
                Log::error("JSON valid, tetapi struktur tidak lengkap. Konten: " . $jsonString);
                throw new \Exception("Struktur JSON dari OpenAI tidak lengkap.");
            }

            // 6. Simpan Hasil
            $this->report->update([
                'match_score' => $aiResult['match_score'] ?? 0,
                'ai_feedback' => $aiResult, // Simpan array (harus di-cast/bersifat JSON)
                'status' => 'completed',
            ]);
        } catch (\Throwable $th) {
            $this->report->update([
                'status' => 'failed'
            ]);

            logger()->error('AI analysis failed : ' . $th->getMessage(), [
                'report_id' => $this->report->id
            ]);

            throw $th; // Lempar kembali agar Job ditandai gagal
        }
    }

    private function generatePrompt(string $resumeText, string $jobText): array
    {
        // PERUBAHAN KRITIS: Instruksi WAJIB menggunakan Bahasa Indonesia
        $systemPrompt = "Anda adalah Recruiter AI yang profesional dan ketat, berspesialisasi dalam analisis ATS (Applicant Tracking System). Tugas Anda adalah membandingkan CV dengan deskripsi pekerjaan. OUTPUT HARUS DIBERIKAN DALAM BAHASA INDONESIA dan WAJIB dalam FORMAT JSON yang valid.";

        $userPrompt = "Analisis Deskripsi Pekerjaan (JD) dan Resume (CV) berikut.
        
        ---
        JD: \"{$jobText}\"
        ---
        CV: \"{$resumeText}\"
        ---

        Sediakan output JSON yang ketat dengan kunci-kunci berikut. SEMUA NILAI STRING HARUS DITULIS DALAM BAHASA INDONESIA:
        1. match_score (Integer, 0-100): Skor kompatibilitas keseluruhan.
        2. summary (String): Paragraf singkat (maks 3 kalimat) yang menjelaskan skor tersebut.
        3. missing_keywords (Array of String): Daftar 3-5 kata kunci/keahlian dari JD yang sangat kurang di CV.
        4. suggestions (Array of String): Daftar 2-3 saran yang dapat ditindaklanjuti bagi kandidat untuk meningkatkan CV mereka agar sesuai dengan JD ini.";

        return [
            'system_prompt' => $systemPrompt,
            'user_prompt' => $userPrompt,
        ];
    }
}
