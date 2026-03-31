<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'seed_key')) {
                $table->string('seed_key')->nullable()->after('slug');
                $table->unique('seed_key', 'categories_seed_key_unique');
            }
        });

        Schema::table('service_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('service_groups', 'seed_key')) {
                $table->string('seed_key')->nullable()->after('slug');
                $table->unique('seed_key', 'service_groups_seed_key_unique');
            }
        });

        Schema::table('service_types', function (Blueprint $table) {
            if (!Schema::hasColumn('service_types', 'seed_key')) {
                $table->string('seed_key')->nullable()->after('slug');
                $table->unique('seed_key', 'service_types_seed_key_unique');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'seed_key')) {
                $table->string('seed_key')->nullable()->after('slug');
                $table->unique('seed_key', 'services_seed_key_unique');
            }
        });

        Schema::table('service_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('service_variants', 'seed_key')) {
                $table->string('seed_key')->nullable()->after('slug');
                $table->unique('seed_key', 'service_variants_seed_key_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_variants', function (Blueprint $table) {
            if (Schema::hasColumn('service_variants', 'seed_key')) {
                $table->dropUnique('service_variants_seed_key_unique');
                $table->dropColumn('seed_key');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'seed_key')) {
                $table->dropUnique('services_seed_key_unique');
                $table->dropColumn('seed_key');
            }
        });

        Schema::table('service_types', function (Blueprint $table) {
            if (Schema::hasColumn('service_types', 'seed_key')) {
                $table->dropUnique('service_types_seed_key_unique');
                $table->dropColumn('seed_key');
            }
        });

        Schema::table('service_groups', function (Blueprint $table) {
            if (Schema::hasColumn('service_groups', 'seed_key')) {
                $table->dropUnique('service_groups_seed_key_unique');
                $table->dropColumn('seed_key');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'seed_key')) {
                $table->dropUnique('categories_seed_key_unique');
                $table->dropColumn('seed_key');
            }
        });
    }
};
