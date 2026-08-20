<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['conversation_id', 'user_id', 'body'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getBodyHtmlAttribute(): string
    {
        return preg_replace_callback(
            '/(https?:\/\/[^\s<]+)/',
            fn ($matches) => '<a href="' . $matches[1] . '" target="_blank" rel="noopener noreferrer" class="underline">' . $matches[1] . '</a>',
            e($this->body)
        );
    }
}
