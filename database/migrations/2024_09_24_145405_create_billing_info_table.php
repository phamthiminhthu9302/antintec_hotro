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
        Schema::create('billing_info', function (Blueprint $table) {
            $table->id('billing_id');
            $table->foreignId('customer_id')->constrained('users', 'user_id');
            $table->enum('payment_method', ['credit_card', 'e_wallet', 'cash']);
            $table->string('card_number');
            $table->string('card_holder_name');
            $table->date('card_expiration_date');
            $table->string('card_security_code');
            $table->string('billing_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_info');
    }
};
