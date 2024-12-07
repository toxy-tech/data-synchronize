<?php

namespace ToxyTech\DataSynchronize\Providers;

use ToxyTech\Base\Facades\DashboardMenu;
use ToxyTech\Base\Facades\PanelSectionManager as PanelSectionManagerFacade;
use ToxyTech\Base\Supports\ServiceProvider;
use ToxyTech\Base\Traits\LoadAndPublishDataTrait;
use ToxyTech\DataSynchronize\Commands\ClearChunksCommand;
use ToxyTech\DataSynchronize\Commands\ExportCommand;
use ToxyTech\DataSynchronize\Commands\ExportControllerMakeCommand;
use ToxyTech\DataSynchronize\Commands\ExporterMakeCommand;
use ToxyTech\DataSynchronize\Commands\ImportCommand;
use ToxyTech\DataSynchronize\Commands\ImportControllerMakeCommand;
use ToxyTech\DataSynchronize\Commands\ImporterMakeCommand;
use ToxyTech\DataSynchronize\PanelSections\ExportPanelSection;
use ToxyTech\DataSynchronize\PanelSections\ImportPanelSection;
use Illuminate\Console\Scheduling\Schedule;

class DataSynchronizeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('packages/data-synchronize')
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishConfigurations(['data-synchronize'])
            ->loadAndPublishViews()
            ->publishAssets()
            ->registerPanelSection()
            ->registerDashboardMenu();

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImporterMakeCommand::class,
                ExporterMakeCommand::class,
                ImportControllerMakeCommand::class,
                ExportControllerMakeCommand::class,
                ClearChunksCommand::class,
                ExportCommand::class,
                ImportCommand::class,
            ]);

            $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
                $schedule
                    ->command(ClearChunksCommand::class)
                    ->dailyAt('00:00');
            });
        }
    }

    protected function getPath(?string $path = null): string
    {
        return __DIR__ . '/../..' . ($path ? '/' . ltrim($path, '/') : '');
    }

    protected function registerPanelSection(): self
    {
        PanelSectionManagerFacade::group('data-synchronize')->beforeRendering(function () {
            PanelSectionManagerFacade::default()
                ->register(ExportPanelSection::class)
                ->register(ImportPanelSection::class);
        });

        return $this;
    }

    protected function registerDashboardMenu(): self
    {
        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-packages-data-synchronize',
                    'parent_id' => 'cms-core-tools',
                    'priority' => 9000,
                    'name' => 'packages/data-synchronize::data-synchronize.tools.export_import_data',
                    'icon' => 'ti ti-package-import',
                    'route' => 'tools.data-synchronize',
                ]);
        });

        return $this;
    }
}
