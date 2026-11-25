<?php

use App\Modules\Gantt\Controllers\ProjectGanttController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProjectGanttController::class, 'main'])
    ->name('preview');

Route::get('/data', [ProjectGanttController::class, 'data'])
    ->name('data');

Route::post('/duration', [ProjectGanttController::class, 'calculateDuration'])
    ->name('duration');

Route::post('/task', [ProjectGanttController::class, 'store'])
    ->name('task.store');
 
Route::post('/import', [ProjectGanttController::class, 'storeImport'])
    ->name('import.store');

Route::post('/import/preview', [ProjectGanttController::class, 'previewImport'])
    ->name('import.preview');

Route::put('/task/{id}', [ProjectGanttController::class, 'update'])
    ->name('task.update');

Route::delete('/task/{id}', [ProjectGanttController::class, 'destroy'])
    ->name('task.destroy');
