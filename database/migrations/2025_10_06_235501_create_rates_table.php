<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'crypto' or 'gift_card'
            $table->string('name'); // e.g., 'Bitcoin', 'Amazon Gift Card'
            $table->string('code')->unique(); // e.g., 'BTC', 'AMAZON_USD'
            $table->decimal('rate', 20, 8); // Exchange rate
            $table->string('currency', 3)->default('NGN'); // Base currency
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
