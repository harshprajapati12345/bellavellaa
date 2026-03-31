<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time cleanup migration.
 * Strips host prefixes and /storage/ prefixes from all image-bearing columns,
 * leaving only the disk-relative path (e.g. 'media/abc.jpg').
 *
 * down() is intentionally a no-op — this migration is safe and irreversible.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Normalize each row individually to apply full normalizer logic
        $this->normalizeColumn('media', 'url');
        $this->normalizeColumn('media', 'thumbnail');
        $this->normalizeColumn('homepage_contents', 'image');
    }

    private function normalizeColumn(string $table, string $column): void
    {
        $rows = DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select('id', $column)
            ->get();

        $fixed = 0;

        foreach ($rows as $row) {
            $original = $row->$column;
            $normalized = $this->normalize($original);

            // Only update if value actually changed
            if ($normalized !== $original) {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update([$column => $normalized]);
                $fixed++;
            }
        }

        echo PHP_EOL . "  [{$table}.{$column}] {$fixed} rows normalized.";
    }

    /**
     * Same normalization logic as MediaPathNormalizer::normalize()
     * (duplicated here so the migration has no external class dependency)
     */
    private function normalize(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        // Case 1: full URL
        if (preg_match('#^https?://#i', $path)) {
            $host = parse_url($path, PHP_URL_HOST);
            $localHosts = ['localhost', '127.0.0.1', '::1'];

            if (!in_array($host, $localHosts, true)) {
                // External URL → null
                return null;
            }

            // Local URL — extract path after /storage/
            $path = parse_url($path, PHP_URL_PATH) ?? '';
        }

        // Case 2: strip /storage/ or storage/ prefix
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        $path = ltrim($path, '/');

        return $path === '' ? null : $path;
    }

    public function down(): void
    {
        // Intentional no-op — normalization is destructive (drops host info)
        // and cannot be safely reversed without original data.
    }
};
