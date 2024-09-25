<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');
            // Tham chiếu đến bảng users cho người gửi và người nhận
            $table->foreignId('sender_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users', 'user_id')->onDelete('cascade');
            // Tham chiếu đến bảng requests
            $table->foreignId('request_id')->constrained('requests', 'request_id')->onDelete('cascade');
            // Nội dung tin nhắn
            $table->text('message');

            $table->boolean('is_seen')->default(0);
            // Timestamps mặc định cho created_at và updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
