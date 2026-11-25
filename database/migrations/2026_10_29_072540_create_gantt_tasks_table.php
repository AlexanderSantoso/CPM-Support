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
    Schema::create('gantt_tasks', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('account_id');
    $table->unsignedBigInteger('project_id');

    $table->foreignId('parent_id')
        ->nullable()
        ->constrained('gantt_tasks')
        ->cascadeOnDelete();

    $table->string('text');
    $table->dateTime('start_date')->nullable();
    $table->dateTime('end_date')->nullable();
    $table->decimal('duration', 12, 4)->default(0);
    $table->string('duration_unit', 10)->default('day');
    $table->decimal('progress', 5, 4)->default(0);
    $table->string('type', 20)->default('task');
    $table->string('status', 100)->nullable();
    $table->unsignedInteger('sort_order')->default(0);

    $table->timestamps();

    $table->index(['account_id', 'project_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gantt_tasks');
    }
};
