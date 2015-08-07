<?php namespace Neomerx\CorsIlluminate\Providers;

/**
 * Copyright 2015 info@neomerx.com (www.neomerx.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use \Illuminate\Support\ServiceProvider;
use \Neomerx\CorsIlluminate\Settings\Settings;

/**
 * @package Neomerx\CorsIlluminate
 */
class LaravelServiceProvider extends ServiceProvider
{
    /** Config file name without extension */
    const CONFIG_FILE_NAME_WO_EXT = 'cors-illuminate';

    /**
     * @inheritdoc
     */
    protected $defer = false;

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), static::CONFIG_FILE_NAME_WO_EXT);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // publish config
        $publishPath = $this->app->make('path.config') . DIRECTORY_SEPARATOR . static::CONFIG_FILE_NAME_WO_EXT . '.php';
        $this->publishes([
            $this->getConfigPath() => $publishPath,
        ]);

        // load settings
        $settings = $this->app->make('config')->get(static::CONFIG_FILE_NAME_WO_EXT);
        Settings::setSettings($settings);
    }

    /**
     * @return string
     */
    private function getConfigPath()
    {
        $root = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $path = $root . 'config' . DIRECTORY_SEPARATOR . static::CONFIG_FILE_NAME_WO_EXT . '.php';

        return $path;
    }
}
