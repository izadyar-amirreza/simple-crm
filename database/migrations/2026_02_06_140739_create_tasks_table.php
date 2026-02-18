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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('notes')->nullable();

            // call, meeting, follow_up, email
            $table->string('type')->default('follow_up');

            // open, done, canceled
            $table->string('status')->default('open');

            $table->timestamp('due_at')->nullable();

            // assigned user (sales)
            $table->foreignId('assigned_to')
                ->constrained('users')
                ->cascadeOnDelete();

            // optional relation to lead/customer
            $table->foreignId('lead_id')
                ->nullable()
                ->constrained('leads')
                ->nullOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['assigned_to', 'status', 'due_at']);
        });
    }

};
