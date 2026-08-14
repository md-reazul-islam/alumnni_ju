<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory;

    // "education" is treated as uncountable by Eloquent's pluralizer, so the table name must be set explicitly.
    protected $table = 'educations';

    protected $fillable = [
        'alumni_profile_id', 'institution', 'degree', 'field_of_study',
        'start_year', 'end_year', 'description',
    ];

    public function alumniProfile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class);
    }
}
