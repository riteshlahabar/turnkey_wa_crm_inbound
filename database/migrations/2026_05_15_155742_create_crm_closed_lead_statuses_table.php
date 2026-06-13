<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_closed_lead_statuses')) {
            Schema::create('crm_closed_lead_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('color', 20)->default('#6c757d');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('crm_leads', 'closed_status_id')) {
            Schema::table('crm_leads', function (Blueprint $table) {
                $table->unsignedBigInteger('closed_status_id')->nullable()->after('lead_status_id');
            });
        }

        DB::table('crm_closed_lead_statuses')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'Admission Done',
                'color' => '#198754',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Lost',
                'color' => '#dc3545',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Not Interested',
                'color' => '#fd7e14',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('crm_leads', 'closed_status_id')) {
            Schema::table('crm_leads', function (Blueprint $table) {
                $table->dropColumn('closed_status_id');
            });
        }

        Schema::dropIfExists('crm_closed_lead_statuses');
    }
};