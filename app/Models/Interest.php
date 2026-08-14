<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function alumniProfiles(): BelongsToMany
    {
        return $this->belongsToMany(AlumniProfile::class, 'alumni_profile_interest');
    }
}
