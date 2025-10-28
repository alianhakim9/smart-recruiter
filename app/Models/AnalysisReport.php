<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisReport extends Model
{
    protected $table = 'analysis_reports';

    public $incrementing = false;
    protected $keyType = 'string';

    // Status (enum) akan otomatis di-cast sebagai string. Match score sebagai integer.
    protected $casts = [
        'ai_feedback' => 'array', // Krusial: agar JSON field dibaca sebagai PHP array/object
        'match_score' => 'integer',
        'status' => 'string',
    ];

    protected $fillable = [
        'user_id',
        'resume_id',
        'job_description_id',
        'status',
        'match_score',
        'ai_feedback',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function jobDescription(): BelongsTo
    {
        return $this->belongsTo(JobDescription::class);
    }
}
