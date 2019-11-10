<?php

namespace MabenDev\Fieldable;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

/**
 * Class PermissionProvider
 * @package MabenDev\Permissions
 *
 * @author Michael Aben
 */
class FieldableProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/MabenDevFieldableConfig.php',
            'MabenDevFieldable');
    }

    public function boot(Filesystem $filesystem)
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/Config/MabenDevFieldableConfig.php' =>
                config_path('MabenDevFieldableConfig.php'),
        ], 'config');

        // Publish migration
        $this->publishes([
            __DIR__ . '/Migrations/maben_dev_fieldable.php' =>
                database_path('migrations/' . date('Y_m_d_His') . '_maben_dev_fieldable.php'),
        ], 'migration');

//        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
