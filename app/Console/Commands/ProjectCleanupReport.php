<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ProjectCleanupReport extends Command
{
    protected $signature = 'project:cleanup';
    protected $description = 'Escanea vistas y rutas sin uso aparente';

    public function handle()
    {
        $this->checkUnusedViews();
        $this->checkRoutesWithoutController();
    }

    protected function checkUnusedViews()
    {
        $viewsPath = resource_path('views');
        $bladeFiles = collect(File::allFiles($viewsPath))
            ->filter(fn($file) => $file->getExtension() === 'blade.php')
            ->map(fn($file) => str_replace(['/', '\\'], '.', str_replace(['.blade.php', $viewsPath . DIRECTORY_SEPARATOR], '', $file->getRealPath())));

        $usedViews = $this->scanUsedViews();

        $unusedViews = $bladeFiles->diff($usedViews);

        $this->info("\n🖼️ Vistas sin referencia:");
        $unusedViews->each(fn($view) => $this->line("- $view"));
    }

    protected function scanUsedViews()
    {
        $used = collect();
        $path = base_path('app');
        $files = File::allFiles($path);

        foreach ($files as $file) {
            $contents = File::get($file->getRealPath());
            preg_match_all("/view\(['\"](.*?)['\"]\)/", $contents, $matches);
            $used->push(...$matches[1]);
        }

        return $used->unique();
    }

    protected function checkRoutesWithoutController()
    {
        $routes = Route::getRoutes();
        $this->info("\n🌐 Rutas sin controlador:");
        foreach ($routes as $route) {
            $action = $route->getAction();
            if (isset($action['uses']) && is_string($action['uses'])) {
                if (!method_exists(...explode('@', $action['uses']))) {
                    $this->line("- " . $route->uri() . " → " . $action['uses']);
                }
            }
        }
    }
}
