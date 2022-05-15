<?php

namespace MityDigital\Iconamic\UpdateScripts\v1_0_2;

use Illuminate\Support\Facades\Artisan;
use Statamic\UpdateScripts\UpdateScript;

class MoveConfigFile extends UpdateScript
{
    public function shouldUpdate($newVersion, $oldVersion)
    {
        return $this->isUpdatingTo('1.0.2');
    }

    public function update()
    {
        // check if the config is cached
        if ($configurationIsCached = app()->configurationIsCached()) {
            Artisan::call('config:clear');
        }

        // if the config file exists within the 'config/statamic' path, move it just to 'config'
        if (file_exists(config_path('statamic/iconamic.php'))) {
            if (file_exists(config_path('iconamic.php'))) {
                // cannot copy
                $this->console()->alert('The Iconamic config file could not be moved to `config/iconamic.php` - it already exists!');
                $this->console()->alert('You will need to manually make sure your `config/iconamic.php` file is correctly configured.');
            } else {
                // move the config file
                rename(config_path('statamic/iconamic.php'), config_path('iconamic.php'));

                // output
                $this->console()->info('Iconamic config file has been moved to `config/iconamic.php`!');
            }
        }

        // re-cache config if it was cached
        if ($configurationIsCached) {
            Artisan::call('config:cache');
        }
    }
}