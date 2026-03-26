<?php

namespace App\Support;

class MediaPathNormalizer
{
    public static function normalize(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (preg_match('#^https?://#i', $path)) {
            $host = parse_url($path, PHP_URL_HOST);
            $localHosts = ['localhost', '127.0.0.1', '::1'];

            if (!in_array($host, $localHosts, true)) {
                return null;
            }

            $urlPath = parse_url($path, PHP_URL_PATH) ?? '';
            $path = $urlPath;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        $path = ltrim($path, '/');

        return $path === '' ? null : $path;
    }

    public static function url(?string $relativePath): ?string
    {
        if ($relativePath === null || trim($relativePath) === '') {
            return null;
        }

        $normalized = self::normalize($relativePath);

        if ($normalized === null && preg_match('#^https?://#i', trim($relativePath))) {
            return trim($relativePath);
        }

        if ($normalized === null) {
            return null;
        }

        return '/storage/' . ltrim($normalized, '/');
    }
}
