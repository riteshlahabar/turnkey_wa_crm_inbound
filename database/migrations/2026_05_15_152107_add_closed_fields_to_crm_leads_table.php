<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('crm_leads', 'is_closed')) {
            Schema::table('crm_leads', function (Blueprint $table) {
                $table->boolean('is_closed')->default(false)->after('lead_status_id');
            });
        }

        if (!Schema::hasColumn('crm_leads', 'closed_at')) {
            Schema::table('crm_leads', function (Blueprint $table) {
                $table->timestamp('closed_at')->nullable()->after('is_closed');
            });
        }

        if (!Schema::hasColumn('crm_leads', 'closed_by')) {
            Schema::table('crm_leads', function (Blueprint $table) {
                $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');
            });
        }

        if (!Schema::hasColumn('crm_leads', 'closed_note')) {
            Schema::table('crm_leads', function (Blueprint $table) {
                $table->text('closed_note')->nullable()->after('closed_by');
            });
        }
    }

    public function down(): void
    {
        $columns = [];

        foreach (['closed_note', 'closed_by', 'closed_at', 'is_closed'] as $column) {
            if (Schema::hasColumn('crm_leads', $column)) {
                $columns[] = $column;
            }
        }

        if (!empty($columns)) {
            Schema::table('crm_leads', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};