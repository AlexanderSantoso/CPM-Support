<?php


namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;



class ModuleLoader
{
    /*
    |--------------------------------------------------------------------------
    | Scan All Modules Recursively
    |--------------------------------------------------------------------------
    | Detect every folder that contains Routes/web.php or Routes/api.php
    */

    protected static function modules(): array
    {
        $modules = [];

        $base = app_path('Modules');

        if (!is_dir($base)) return [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if (!$file->isDir()) continue;

            if ($file->getFilename() === 'Routes') {
                $modules[] = dirname($file->getPathname());
            }
        }

        sort($modules);

        return $modules;
    }

    /*
    |--------------------------------------------------------------------------
    | Load WEB Routes
    |--------------------------------------------------------------------------
    */

    public static function web(): void
    {
        if (app()->routesAreCached()) return;

        foreach (self::modules() as $modulePath) {

            $file = $modulePath.'/Routes/web.php';
            if (!file_exists($file)) continue;

            $prefix = self::modulePrefix($modulePath);
            $name   = self::moduleName($modulePath);

            Route::middleware('web')->prefix($prefix)->name("$name.")->group($file);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Load API Routes
    |--------------------------------------------------------------------------
    */

    public static function api(): void
    {
        if (app()->routesAreCached()) return;

        foreach (self::modules() as $modulePath) {

            $file = $modulePath.'/Routes/api.php';
            if (!file_exists($file)) continue;

            $prefix = self::modulePrefix($modulePath);
            $name   = self::moduleName($modulePath);

            Route::middleware('api')->prefix("api/$prefix")->name("api.$name.")->group($file);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Convert Module Path => URL Prefix
    |--------------------------------------------------------------------------
    | Converts a module path into a URL-friendly prefix using kebab-case.
    |
    | Examples:
    | Modules/Admin/ProjectCategory      => admin/project-category
    | Modules/Dashboard                  => dashboard
    | Modules/CostAndContract/RFP        => cost-and-contract/rfp
    | Modules/CostAndContract/BudgetPlan => cost-and-contract/budget-plan
    */
    protected static function modulePrefix(string $path): string
    {
        $relative = str_replace(
            app_path('Modules').DIRECTORY_SEPARATOR,
            '',
            $path
        );

        $segments = explode(DIRECTORY_SEPARATOR, $relative);

        return implode('/', array_map(
            fn ($segment) => self::normalizeSegment($segment),
            $segments
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Convert Module Path => Route Name
    |--------------------------------------------------------------------------
    | Converts a module path into a route name using dot notation and kebab-case formatting.
    |
    | Examples:
    | Modules/Admin/ProjectCategory      => admin.project-category
    | Modules/Dashboard                  => dashboard
    | Modules/CostAndContract/RFP        => cost-and-contract.rfp
    | Modules/CostAndContract/BudgetPlan => cost-and-contract.budget-plan
    */
    protected static function moduleName(string $path): string
    {
        $relative = str_replace(
            app_path('Modules').DIRECTORY_SEPARATOR,
            '',
            $path
        );

        $segments = explode(DIRECTORY_SEPARATOR, $relative);

        return implode('.', array_map(
            fn ($segment) => self::normalizeSegment($segment),
            $segments
        ));
    }

    protected static function normalizeSegment(string $segment): string
    {
        if (preg_match('/^[A-Z0-9]+$/', $segment)) {
            return strtolower($segment);
        }

        return Str::kebab($segment);
    }

}
