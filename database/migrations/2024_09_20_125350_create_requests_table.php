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
        Schema::create('requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('customer_id')->constrained('users', 'user_id');
            $table->foreignId('technician_id')->nullable()->constrained('users', 'user_id');
            $table->foreignId('service_id')->constrained('services', 'service_id');
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->text('photo')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled']);
            $table->text('location')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
