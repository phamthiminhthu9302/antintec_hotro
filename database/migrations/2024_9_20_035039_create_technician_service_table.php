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
        Schema::create('technician_service', function (Blueprint $table) {
            $table->unsignedBigInteger('technician_id');
            $table->unsignedBigInteger('service_id');

            // Tạo khóa chính tổ hợp từ cả hai cột
            $table->primary(['technician_id', 'service_id']);
            
            // Khóa ngoại tham chiếu đến bảng technicians và services
            $table->foreign('technician_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_service');
    }
};
