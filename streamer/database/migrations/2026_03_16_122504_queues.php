<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('queues', function (Blueprint $table) {
        $table->id();
        
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->foreignId('schedule_id')->constrained('stream_schedules')->onDelete('cascade');

        $table->string('mlbb_id');
        $table->string('mlbb_server');
        $table->string('nickname');
        $table->enum('status', ['pending', 'playing', 'completed', 'skipped'])->default('pending');
        
        $table->integer('queue_number')->nullable();

        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};