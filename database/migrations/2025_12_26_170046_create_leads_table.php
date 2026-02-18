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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('source')->nullable(); // instagram, referral, ...
            $table->string('status')->default('new'); // new, contacted, qualified, converted, lost

            // مالک لید (sales یا admin)
            $table->foreignId('owner_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // تبدیل به customer
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
