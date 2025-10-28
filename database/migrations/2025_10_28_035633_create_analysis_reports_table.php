<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analysis_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->foreignUuid('resume_id')
                ->constrained('resumes', 'id')
                ->cascadeOnUpdate();
            $table->foreignUuid('job_description_id')
                ->constrained('job_descriptions', 'id')
                ->cascadeOnDelete();
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed'
            ]);
            $table->integer('match_score')->nullable();
            $table->json('ai_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_reports');
    }
};
