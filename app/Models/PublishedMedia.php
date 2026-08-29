<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishedMedia extends Model
{
    use HasFactory;

    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';

    protected $fillable = ['title', 'type', 'image', 'video_url', 'tag', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/', $this->video_url, $matches)) {
            return "https://www.youtube.com/embed/{$matches[1]}";
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $matches)) {
            return "https://player.vimeo.com/video/{$matches[1]}";
        }

        return null;
    }

    public function getVideoThumbnailUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/', $this->video_url, $matches)) {
            return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
        }

        return null;
    }
}
