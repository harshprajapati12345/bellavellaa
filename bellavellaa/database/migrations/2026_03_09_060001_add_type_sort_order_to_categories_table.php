<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // type: 'services' categories show group/service flow
            //       'packages' categories show packages listing (e.g. Bride)
            // Temporary default for existing rows — validation makes it required at app level
            $table->enum('type', ['services', 'packages'])->default('services')->after('slug');
            $table->unsignedInteger('sort_order')->default(0)->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['type', 'sort_order']);
        });
    }
};
