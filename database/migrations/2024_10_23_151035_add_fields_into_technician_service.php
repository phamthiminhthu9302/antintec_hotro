<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('technician_service', function (Blueprint $table) {
            //
            //if inactive and available from is in the future, then
            $table->timestamp('available_from');
            $table->timestamp('available_to')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technician_service', function (Blueprint $table) {
            //
            $table->dropColumn('available_from');
            $table->dropColumn('available_to');
            $table->dropColumn('status');
        });
    }
};
