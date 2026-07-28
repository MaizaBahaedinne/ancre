<?php

namespace App\Support;

use Illuminate\Support\Str;

class AvatarUrl
{
    public static function fromPath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $normalized = trim(str_replace('\\', '/', $path));

        if ($normalized === '') {
            return null;
        }

        if (Str::startsWith($normalized, ['http://', 'https://'])) {
            return $normalized;
        }

        if (Str::startsWith($normalized, '/storage/')) {
            return asset(ltrim($normalized, '/'));
        }

        if (Str::startsWith($normalized, 'storage/')) {
            return asset($normalized);
        }

        if (Str::startsWith($normalized, 'public/')) {
            $normalized = Str::after($normalized, 'public/');
        }

        return asset('storage/'.ltrim($normalized, '/'));
    }
}
