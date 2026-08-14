<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Degree extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'abbreviation'];

    public function alumniProfiles(): HasMany
    {
        return $this->hasMany(AlumniProfile::class);
    }
}
