<?php
use Illuminate\Support\Facades\Route;
use App\Support\ModuleLoader;

Route::get('/', function () {
    return redirect('dashboard');
});

ModuleLoader::web();
