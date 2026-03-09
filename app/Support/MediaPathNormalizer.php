<?php

namespace App\Support;

/**
 * MediaPathNormalizer
 *
 * Single source of truth for converting any media path/URL into
 * the canonical disk-relative format expected by our system.
 *
 * Target format (stored in DB):   media/banner1.jpg
 * Rejected format (never stored): http://localhost/storage/media/banner1.jpg
 *
 * Usage:
 *   MediaPathNormalizer::normalize($rawPath)  → 'media/banner1.jpg' or null
 *   MediaPathNormalizer::url($relativePath)   → full public URL (API responses only)
 */
class MediaPathNormalizer
{
    /**
     * Normalize any path/URL to a disk-relative path suitable for DB storage.
     *
     * Handles these cases:
     *  null                                      → null
     *  'media/abc.jpg'                           → 'media/abc.jpg'   (already clean)
     *  'storage/media/abc.jpg'                   → 'media/abc.jpg'   (strip storage/ prefix)
     *  '/storage/media/abc.jpg'                  → 'media/abc.jpg'   (strip /storage/ prefix)
     *  'http://localhost/storage/media/abc.jpg'  → 'media/abc.jpg'   (strip host + /storage/)
     *  'http://127.0.0.1:PORT/storage/media/...' → 'media/abc.jpg'   (strip host + /storage/)
     *  'https://external.com/image.png'          → null              (external URL rejected)
     */
    public static function normalize(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        // ── Case 1: full URL (http:// or https://) ─────────────────────────
        if (preg_match('#^https?://#i', $path)) {
            // Parse host to decide if this is our local server
            $host = parse_url($path, PHP_URL_HOST);
            $localHosts = ['localhost', '127.0.0.1', '::1'];

            if (!in_array($host, $localHosts, true)) {
                // External URL — reject (return null)
                return null;
            }

            // Local URL — extract path portion after /storage/
            $urlPath = parse_url($path, PHP_URL_PATH) ?? '';
            $path = $urlPath; // fall through to prefix-stripping below
        }

        // ── Case 2: strip /storage/ or storage/ prefix ─────────────────────
        // e.g. '/storage/media/abc.jpg' → 'media/abc.jpg'
        //      'storage/media/abc.jpg'  → 'media/abc.jpg'
        $path = ltrim($path, '/');                   // remove leading slash
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/')); // strip 'storage/'
        }

        // ── Case 3: remove any remaining leading slash ──────────────────────
        $path = ltrim($path, '/');

        return $path === '' ? null : $path;
    }

    /**
     * Generate a full publicly accessible URL from a disk-relative path.
     *
     * Use ONLY in API Resources — never store the result in DB.
     */
    public static function url(?string $relativePath): ?string
    {
        if ($relativePath === null || trim($relativePath) === '') {
            return null;
        }

        return \Storage::disk('public')->url($relativePath);
    }
}
