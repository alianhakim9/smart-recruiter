<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\PdfToText\Pdf;

class ResumeParserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function parse(UploadedFile $file): ?string
    {
        // 1. Dapatkan path sementara file.
        $temporaryPath = $file->getRealPath();

        try {
            // 2. Gunakan package Spatie untuk mengekstrak teks.
            // Pastikan 'pdftotext' sudah terinstal dan ada di PATH.

            $text = Pdf::getText($temporaryPath);

            // 3. Kembalikan teks yang sudah bersih.
            return trim($text);
        } catch (\Throwable $th) {
            // 4. Logging Error: KRUSIAL! Jika parsing gagal (misal file korup),
            // kita harus tahu mengapa.

            logger()->error("Parsing cv gagal : " . $th->getMessage(), [
                'file_name' => $file->getClientOriginalName(),
                'user_id' => auth()->id() // Log ID user yang gagal.
            ]);

            // Mengembalikan null jika gagal. Component harus menangani ini.
            return null;
        }
    }
}
