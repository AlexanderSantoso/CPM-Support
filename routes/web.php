<?php
use Illuminate\Support\Facades\Route;
use App\Support\ModuleLoader;

// use App\Core\Controllers\ThemeController;
// use App\Core\Controllers\ForbiddenController;

Route::get('/', function () {
    return redirect('login');
});

// Route::middleware(['auth', 'verified'])->group(function () {
//     //Change Theme Color
//     Route::post('theme/change', [ThemeController::class, 'changeTheme'])->name('changeThemeColor');

//     //Forbidden
//     Route::get('forbidden', [ForbiddenController::class, 'index'])->name('forbidden');
// });

// require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Load Module Routes
|--------------------------------------------------------------------------
*/
ModuleLoader::web();
