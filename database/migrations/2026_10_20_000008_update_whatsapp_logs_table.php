<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE whatsapp_logs MODIFY COLUMN message_type VARCHAR(50) NOT NULL DEFAULT 'announcement'");
            DB::statement("ALTER TABLE whatsapp_logs MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'sent'");
        } catch (\Exception $e) {
            // Ignore if already string
        }
    }

    public function down(): void
    {
        // Keep as string for safety
    }
};
