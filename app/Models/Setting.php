<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['group', 'key', 'value'];

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("settings.{$group}.{$key}", function () use ($group, $key, $default) {
            $setting = static::where('group', $group)->where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    public static function set(string $group, string $key, mixed $value): void
    {
        static::updateOrCreate(compact('group', 'key'), ['value' => $value]);

        Cache::forget("settings.{$group}.{$key}");
    }
}
