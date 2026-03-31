<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_contents', function (Blueprint $table) {
            // Only add columns if they don't exist yet
            if (!Schema::hasColumn('homepage_contents', 'name')) {
                $table->string('name')->nullable()->after('section');
            }
            if (!Schema::hasColumn('homepage_contents', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('title');
            }
            if (!Schema::hasColumn('homepage_contents', 'content_type')) {
                $table->string('content_type')->default('dynamic')->after('subtitle');
            }
            if (!Schema::hasColumn('homepage_contents', 'data_source')) {
                $table->string('data_source')->nullable()->after('content_type');
            }
            if (!Schema::hasColumn('homepage_contents', 'media_type')) {
                $table->string('media_type')->default('banner')->after('data_source');
            }
            if (!Schema::hasColumn('homepage_contents', 'description')) {
                $table->text('description')->nullable()->after('media_type');
            }
            if (!Schema::hasColumn('homepage_contents', 'btn_text')) {
                $table->string('btn_text')->nullable()->after('description');
            }
            if (!Schema::hasColumn('homepage_contents', 'btn_link')) {
                $table->string('btn_link')->nullable()->after('btn_text');
            }
        });

        // Backfill: extract JSON content fields into flat columns
        $rows = DB::table('homepage_contents')->get();
        foreach ($rows as $row) {
            $content = json_decode($row->content, true) ?? [];

            DB::table('homepage_contents')->where('id', $row->id)->update([
                'name'         => $content['name']         ?? $row->name         ?? null,
                'subtitle'     => $content['subtitle']     ?? $row->subtitle     ?? null,
                'content_type' => $content['content_type'] ?? $row->content_type ?? 'dynamic',
                'data_source'  => $content['data_source']  ?? $row->data_source  ?? null,
                'media_type'   => $content['media_type']   ?? $row->media_type   ?? 'banner',
                'description'  => $content['description']  ?? $row->description  ?? null,
                'btn_text'     => $content['btn_text']     ?? $row->btn_text     ?? null,
                'btn_link'     => $content['btn_link']     ?? $row->btn_link     ?? null,
                // Clean up the content JSON — keep only items and extra configs
                'content' => json_encode(array_diff_key(
                    $content,
                    array_flip(['name','subtitle','content_type','data_source','media_type','description','btn_text','btn_link'])
                )),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('homepage_contents', function (Blueprint $table) {
            $table->dropColumn(['name','subtitle','content_type','data_source','media_type','description','btn_text','btn_link']);
        });
    }
};
