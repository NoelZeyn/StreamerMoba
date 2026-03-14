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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('donator_name');
            $table->string('email')->nullable();
            $table->text('message')->nullable();

            $table->unsignedBigInteger('amount');
            $table->string('currency')->default('IDR');

            $table->string('saweria_id')->nullable()->index();

            $table->json('raw_payload')->nullable();

            $table->timestamp('donated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
