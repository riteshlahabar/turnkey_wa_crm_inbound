<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('crm_followups', 'assigned_user_id')) {
            Schema::table('crm_followups', function (Blueprint $table) {
                $table->unsignedBigInteger('assigned_user_id')
                    ->nullable()
                    ->after('followup_type_id');
            });
        }

        DB::statement("
            UPDATE crm_followups f
            JOIN crm_leads l ON l.id = f.lead_id
            JOIN users u ON u.id = l.assigned_user_id
            SET f.assigned_user_id = l.assigned_user_id
            WHERE l.assigned_user_id IS NOT NULL
            AND u.role != 'admin'
        ");
    }

    public function down(): void
    {
        if (Schema::hasColumn('crm_followups', 'assigned_user_id')) {
            Schema::table('crm_followups', function (Blueprint $table) {
                $table->dropColumn('assigned_user_id');
            });
        }
    }
};