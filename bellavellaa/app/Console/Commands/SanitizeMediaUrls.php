<?php

namespace App\Console\Commands;

use App\Support\MediaPathNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SanitizeMediaUrls extends Command
{
    protected $signature = 'media:sanitize-urls {--dry-run : Show what would be changed without making any changes}';
    protected $description = 'Find and fix any media rows that contain full URLs instead of disk-relative paths.';

    /** Columns to check: [table => [columns]] */
    private array $targets = [
        'media'              => ['url', 'thumbnail'],
        'homepage_contents'  => ['image'],
    ];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $totalFixed = 0;
        $totalDirty = 0;

        if ($isDryRun) {
            $this->warn('DRY RUN — no changes will be made.');
        }

        foreach ($this->targets as $table => $columns) {
            foreach ($columns as $column) {
                [$dirty, $fixed] = $this->processColumn($table, $column, $isDryRun);
                $totalDirty += $dirty;
                $totalFixed += $fixed;
            }
        }

        $this->newLine();

        if ($totalDirty === 0) {
            $this->info('✅ All image paths are clean. Nothing to fix.');
            return Command::SUCCESS;
        }

        if ($isDryRun) {
            $this->warn("Found {$totalDirty} dirty row(s). Re-run without --dry-run to fix.");
        } else {
            $this->info("Fixed {$totalFixed} / {$totalDirty} dirty row(s).");
        }

        return $totalDirty > 0 && !$isDryRun && $totalFixed < $totalDirty
            ? Command::FAILURE
            : Command::SUCCESS;
    }

    private function processColumn(string $table, string $column, bool $isDryRun): array
    {
        $rows = DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select('id', $column)
            ->get();

        $dirty = 0;
        $fixed = 0;

        foreach ($rows as $row) {
            $original = $row->$column;
            $normalized = MediaPathNormalizer::normalize($original);

            if ($normalized === $original) {
                continue; // already clean
            }

            $dirty++;
            $this->line(sprintf(
                '  [%s.%s] id=%s  %s → %s',
                $table,
                $column,
                $row->id,
                $original,
                $normalized ?? 'null'
            ));

            if (!$isDryRun) {
                DB::table($table)->where('id', $row->id)->update([$column => $normalized]);
                $fixed++;
            }
        }

        if ($dirty === 0) {
            $this->line("  <fg=green>[{$table}.{$column}]</> ✓ clean");
        }

        return [$dirty, $fixed];
    }
}
