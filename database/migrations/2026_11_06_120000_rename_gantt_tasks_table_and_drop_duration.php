<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $oldTable = 'gantt_tasks';
        $newTable = 'schedule_master_schedule_tasks';

        if (Schema::hasTable($oldTable) && !Schema::hasTable($newTable)) {
            Schema::rename($oldTable, $newTable);
        }

        if (Schema::hasTable($newTable) && Schema::hasColumn($newTable, 'duration')) {
            Schema::table($newTable, function (Blueprint $table) {
                $table->dropColumn('duration');
            });
        }
    }

    public function down(): void
    {
        $oldTable = 'gantt_tasks';
        $newTable = 'schedule_master_schedule_tasks';

        if (Schema::hasTable($newTable)) {
            if (!Schema::hasColumn($newTable, 'duration')) {
                Schema::table($newTable, function (Blueprint $table) {
                    $table->decimal('duration', 12, 4)->default(0);
                });
            }

            if (!Schema::hasTable($oldTable)) {
                Schema::rename($newTable, $oldTable);
            }
        }
    }
};
