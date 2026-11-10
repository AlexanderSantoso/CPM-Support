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
      Schema::create('schedule_master_schedule_task_links', function (Blueprint $table) {
        $table->id();
        $table->foreignId('source_task_id')
            ->constrained('schedule_master_schedule_tasks')
            ->cascadeOnDelete();
        $table->foreignId('target_task_id')
            ->constrained('schedule_master_schedule_tasks')
            ->cascadeOnDelete();
        $table->unsignedTinyInteger('type')->default(0);
        $table->timestamps();  
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_master_schedule_task_links');
    }
};
