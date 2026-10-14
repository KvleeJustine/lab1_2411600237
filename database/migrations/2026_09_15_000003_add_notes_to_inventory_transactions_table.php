<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // notes is already included in the 000002 migration for fresh installs.
        // This migration only runs if upgrading from a Lab 5 database that
        // had inventory_transactions without the notes column.
        if (! Schema::hasColumn('inventory_transactions', 'notes')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('reference');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventory_transactions', 'notes')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }
};
