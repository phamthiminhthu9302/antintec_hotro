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
        Schema::create('technician_details', function (Blueprint $table) {
            $table->id('detail_id');
            $table->foreignId('technician_id')->constrained('users', 'user_id'); // Tham chiếu đến bảng users với vai trò là kỹ thuật viên
            $table->text('skills')->nullable(); // Kỹ năng của kỹ thuật viên (có thể là một chuỗi hoặc JSON)
            $table->text('certifications')->nullable(); // Chứng chỉ của kỹ thuật viên
            $table->string('work_area', 255)->nullable();
            $table->decimal('amount', 10, 2)->nullable(); // Khu vực làm việc của kỹ thuật viên (có thể là tên thành phố hoặc GPS)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_details');
    }
};
