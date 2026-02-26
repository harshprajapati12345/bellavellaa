<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('subcategory')->nullable()->after('category');
            $table->json('service_types')->nullable()->after('description');
            $table->string('desc_title')->nullable()->after('service_types');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['subcategory', 'service_types', 'desc_title']);
        });
    }
};
