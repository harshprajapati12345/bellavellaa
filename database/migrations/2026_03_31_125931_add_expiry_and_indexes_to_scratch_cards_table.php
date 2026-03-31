<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('scratched_at');
            $table->index(['customer_id', 'is_scratched']);
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('scratch_cards', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'is_scratched']);
            $table->dropIndex(['source']);
            $table->dropColumn('expires_at');
        });
    }

};
