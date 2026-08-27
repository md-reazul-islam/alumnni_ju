<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['last_message_at', 'context'];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('last_read_at')->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Which inbox category this conversation belongs to, from the viewing user's
     * perspective. Assumes $this->participants is already loaded (and, for the
     * Alumni/Mentors case, filtered to exclude $viewer) — callers driving a list of
     * conversations should eager-load participants once rather than per-conversation.
     */
    public function categoryLabel(User $viewer): string
    {
        if ($this->context === 'matrimony') {
            return 'Matrimony';
        }

        if ($this->subject_type === MarketplaceOrder::class) {
            return 'Marketplace';
        }

        if ($this->subject_type === CateringHomemadeOrder::class) {
            return 'Catering';
        }

        $other = $this->participants->first(fn ($p) => $p->id !== $viewer->id) ?? $this->participants->first();

        return $other?->isMentor() ? 'Mentors' : 'Alumni';
    }
}
