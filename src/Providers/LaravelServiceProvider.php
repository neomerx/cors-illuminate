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

use \Closure;
use \Neomerx\Cors\Analyzer;
use \Psr\Log\LoggerInterface;
use \Illuminate\Support\ServiceProvider;
use \Illuminate\Contracts\Config\Repository;
use \Neomerx\Cors\Contracts\AnalyzerInterface;
use \Neomerx\CorsIlluminate\Settings\Settings;
use \Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use \Illuminate\Contracts\Foundation\Application as ApplicationInterface;

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
     * @var bool|null|LoggerInterface
     */
    private $logger = false;

    /**
     * @var bool|array
     */
    private $settings = false;

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), static::CONFIG_FILE_NAME_WO_EXT);
        $this->configureCorsAnalyzer();
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishConfig();
    }

    /**
     * @return void
     */
    protected function registerPublishConfig()
    {
        $publishPath = $this->app['path.config'] . DIRECTORY_SEPARATOR . static::CONFIG_FILE_NAME_WO_EXT . '.php';
        $this->publishes([
            $this->getConfigPath() => $publishPath,
        ]);
    }

    /**
     * @return void
     */
    protected function configureCorsAnalyzer()
    {
        $this->app->bind(AnalysisStrategyInterface::class, $this->getCreateAnalysisStrategyClosure());
        $this->app->bind(AnalyzerInterface::class, $this->getCreateAnalyzerClosure());
    }

    /**
     * @return Closure
     */
    protected function getCreateAnalysisStrategyClosure()
    {
        return function ($app) {
            $settings = $this->getSettings($app);
            $strategy = new Settings($settings);

            return $strategy;
        };
    }

    /**
     * @return Closure
     */
    protected function getCreateAnalyzerClosure()
    {
        return function ($app) {
            /** @var AnalysisStrategyInterface $strategy */
            $strategy = $app[AnalysisStrategyInterface::class];
            $analyzer = Analyzer::instance($strategy);

            $logger = $this->getLoggerIfEnabled($app);
            $logger === null ?: $analyzer->setLogger($logger);

            return $analyzer;
        };
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

    /**
     * @param ApplicationInterface $app
     *
     * @return null|LoggerInterface
     */
    private function getLoggerIfEnabled($app)
    {
        /** @var ApplicationInterface $app */

        if ($this->logger === false) {
            $settings       = $this->getSettings($app);
            $loggingEnabled =
                array_key_exists(Settings::KEY_LOGS_ENABLED, $settings) === true &&
                $settings[Settings::KEY_LOGS_ENABLED] === true;

            $this->logger = $loggingEnabled === true ? $app[LoggerInterface::class] : null;
        }

        return $this->logger;
    }

    /**
     * @param ApplicationInterface $app
     *
     * @return array
     */
    private function getSettings($app)
    {
        /** @var ApplicationInterface $app */

        if ($this->settings === false) {
            /** @var Repository $config */
            $config         = $app['config'];
            $this->settings = $config->get(static::CONFIG_FILE_NAME_WO_EXT, []);
        }

        return $this->settings;
    }
}
