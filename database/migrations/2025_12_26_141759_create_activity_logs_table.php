<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic subject (Customer / Lead / ...)
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');

            $table->string('action'); // created, updated, deleted, restored, force_deleted, lead_converted, ...

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
