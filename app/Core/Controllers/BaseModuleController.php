<?php

namespace App\Core\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Services\CryptoService;

abstract class BaseModuleController extends Controller
{
    private ?CryptoService $cryptoService   = null;

    protected function cryptoService(): CryptoService
    {
        return $this->cryptoService ??= app(CryptoService::class);
    }

    protected function viewModule(string $view, array $data = []): View
    {
        return view(static::MODULE_PATH . $view, $data);
    }

    protected function viewComponent(string $view, array $data = []): View
    {
        return view('components.' . $view, $data);
    }
}
