<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE events MODIFY status ENUM('ACTIVE', 'CANCELLED', 'DRAFT', 'FINISHED') NOT NULL DEFAULT 'DRAFT'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::table('events')->where('status', 'FINISHED')->update(['status' => 'ACTIVE']);

        DB::statement("ALTER TABLE events MODIFY status ENUM('ACTIVE', 'CANCELLED', 'DRAFT') NOT NULL DEFAULT 'DRAFT'");
    }
};
