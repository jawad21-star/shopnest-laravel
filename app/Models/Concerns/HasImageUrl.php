<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasImageUrl
{
    public static function resolveImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : Storage::disk('public')->url($path);
    }
}
