<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->index('user_unique_id');
            $table->index('email');
            $table->index('pat_unique_id');
        });

        Schema::table('medical_visits', function (Blueprint $table) {
            $table->index('doctor_id');
            $table->index('nurse_id');
            $table->index('patient_id');
            $table->index('visit_date');
            $table->index('is_approved');
            $table->index('medical_status');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['user_unique_id']);
            $table->dropIndex(['email']);
            $table->dropIndex(['pat_unique_id']);
        });

        Schema::table('medical_visits', function (Blueprint $table) {
            $table->dropIndex(['doctor_id']);
            $table->dropIndex(['nurse_id']);
            $table->dropIndex(['patient_id']);
            $table->dropIndex(['visit_date']);
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['medical_status']);
            $table->dropIndex(['created_by']);
        });
    }
};
