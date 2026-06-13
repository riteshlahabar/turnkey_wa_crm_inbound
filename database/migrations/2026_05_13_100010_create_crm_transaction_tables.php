<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();

            $table->string('lead_no')->nullable()->unique();

            $table->string('parent_name')->nullable();
            $table->string('student_name')->nullable();

            $table->string('phone')->nullable()->index();
            $table->string('mobile')->nullable()->index();
            $table->string('alternate_mobile')->nullable();

            $table->foreignId('standard_id')->nullable()->constrained('crm_standards')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('crm_courses')->nullOnDelete();

            $table->string('school_name')->nullable();
            $table->string('board')->nullable();
            $table->text('address')->nullable();
            $table->string('area')->nullable();

            $table->foreignId('lead_source_id')->nullable()->constrained('crm_lead_sources')->nullOnDelete();
            $table->foreignId('lead_status_id')->nullable()->constrained('crm_lead_statuses')->nullOnDelete();
            $table->foreignId('lead_priority_id')->nullable()->constrained('crm_lead_priorities')->nullOnDelete();

            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->date('inquiry_date')->nullable();
            $table->dateTime('next_followup_at')->nullable();

            // Required by DashboardController
            $table->dateTime('last_activity_at')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });

        Schema::create('crm_calls', function (Blueprint $table) {
            $table->id();

            $table->string('phone')->index();
            $table->string('caller_name')->nullable();

            $table->string('call_type')->default('incoming');
            $table->dateTime('received_at')->nullable();

            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('crm_courses')->nullOnDelete();

            $table->string('status')->default('new');
            $table->text('notes')->nullable();

            $table->dateTime('whatsapp_sent_at')->nullable();

            $table->timestamps();
        });

        Schema::create('crm_followups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('followup_type_id')->nullable()->constrained('crm_followup_types')->nullOnDelete();

            $table->dateTime('followup_at')->nullable();
            $table->dateTime('next_followup_at')->nullable();

            $table->string('status')->default('pending');
            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });

        Schema::create('crm_whatsapp_logs', function (Blueprint $table) {
            $table->id();

            $table->string('phone')->index();
            $table->string('parent_name')->nullable();

            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('crm_courses')->nullOnDelete();

            $table->text('message')->nullable();
            $table->string('status')->default('sent');
            $table->dateTime('sent_at')->nullable();

            $table->timestamps();
        });

        Schema::create('crm_admissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('crm_courses')->nullOnDelete();
            $table->foreignId('standard_id')->nullable()->constrained('crm_standards')->nullOnDelete();

            $table->decimal('admission_amount', 12, 2)->default(0);
            $table->date('admission_date')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('crm_fee_quotations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('crm_courses')->nullOnDelete();

            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);

            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_fee_quotations');
        Schema::dropIfExists('crm_admissions');
        Schema::dropIfExists('crm_whatsapp_logs');
        Schema::dropIfExists('crm_followups');
        Schema::dropIfExists('crm_calls');
        Schema::dropIfExists('crm_leads');
    }
};