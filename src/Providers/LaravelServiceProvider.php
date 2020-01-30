<?php declare(strict_types = 1);

namespace Neomerx\CorsIlluminate\Providers;

/**
 * Copyright 2015-2020 info@neomerx.com
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

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Neomerx\CorsIlluminate\Settings\Settings;
use Psr\Log\LoggerInterface;

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
     * @var bool|array
     */
    private $settingsData = false;

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->mergeConfigs();
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
     * Merge default config and config from application `config` folder.
     *
     * @return void
     */
    protected function mergeConfigs(): void
    {
        $repo   = $this->getConfigRepository();
        $config = $repo->get(static::CONFIG_FILE_NAME_WO_EXT, []);
        $base   = $this->getBaseConfig();
        $result = $config + $base;
        $repo->set(static::CONFIG_FILE_NAME_WO_EXT, $result);
    }

    /**
     * @return void
     */
    protected function registerPublishConfig(): void
    {
        $publishPath = $this->app['path.config'] . DIRECTORY_SEPARATOR . static::CONFIG_FILE_NAME_WO_EXT . '.php';
        $this->publishes([
            $this->getConfigPath() => $publishPath,
        ]);
    }

    /**
     * @return void
     */
    protected function configureCorsAnalyzer(): void
    {
        $this->app->bind(AnalysisStrategyInterface::class, $this->getCreateAnalysisStrategyClosure());
        $this->app->bind(AnalyzerInterface::class, $this->getCreateAnalyzerClosure());
    }

    /**
     * @return Closure
     */
    protected function getCreateAnalysisStrategyClosure(): Closure
    {
        return function (): AnalysisStrategyInterface {
            $data     = $this->getSettingsData();
            $strategy = (new Settings())->setData($data);

            return $strategy;
        };
    }

    /**
     * @return Closure
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getCreateAnalyzerClosure(): Closure
    {
        return function ($app): AnalyzerInterface {
            /** @var AnalysisStrategyInterface $strategy */
            $strategy = $app[AnalysisStrategyInterface::class];
            $analyzer = Analyzer::instance($strategy);

            /** @var Settings $strategy */

            if ($strategy->isLogEnabled() === true) {
                /** @var LoggerInterface $logger */
                $logger = $app[LoggerInterface::class];
                $analyzer->setLogger($logger);
            }

            return $analyzer;
        };
    }

    /**
     * @return string
     */
    protected function getConfigPath(): string
    {
        $root = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $path = $root . 'config' . DIRECTORY_SEPARATOR . static::CONFIG_FILE_NAME_WO_EXT . '.php';

        return $path;
    }

    /**
     * @return array
     *
     * PHPMD do not work with ['key' => $var] = ...;
     * @SuppressWarnings(PHPMD.UndefinedVariable)
     */
    protected function getSettingsData(): array
    {
        if ($this->settingsData === false) {
            $configFile = $this->getConfigRepository()->get(static::CONFIG_FILE_NAME_WO_EXT, []);

            // server origin should be in parse_url() result format.
            \assert(
                \array_key_exists(Settings::KEY_SERVER_ORIGIN, $configFile) &&
                \is_array($configFile[Settings::KEY_SERVER_ORIGIN]),
                'Server origin must be array in `parse_url()` format.'
            );
            \assert(\array_key_exists('scheme', $configFile[Settings::KEY_SERVER_ORIGIN]));
            \assert(\array_key_exists('host', $configFile[Settings::KEY_SERVER_ORIGIN]));
            \assert(\array_key_exists('port', $configFile[Settings::KEY_SERVER_ORIGIN]));
            [
                'scheme' => $serverOriginScheme,
                'host'   => $serverOriginHost,
                'port'   => $serverOriginPort,
            ] = $configFile[Settings::KEY_SERVER_ORIGIN];

            $settings = new Settings();
            $settings->init($serverOriginScheme, $serverOriginHost, $serverOriginPort);

            $origins = $configFile[Settings::KEY_ALLOWED_ORIGINS] ?? null;
            $origins !== null ? $settings->setAllowedOrigins($origins) : $settings->enableAllOriginsAllowed();

            $settings->setAllowedMethods($configFile[Settings::KEY_ALLOWED_METHODS] ?? []);
            $settings->setAllowedHeaders($configFile[Settings::KEY_ALLOWED_HEADERS] ?? []);
            $settings->setExposedHeaders($configFile[Settings::KEY_EXPOSED_HEADERS] ?? []);
            $settings->setPreFlightCacheMaxAge($configFile[Settings::KEY_FLIGHT_CACHE_MAX_AGE] ?? 0);

            \boolval($configFile[Settings::KEY_IS_USING_CREDENTIALS] ?? false) === true ?
                $settings->setCredentialsSupported() : $settings->setCredentialsNotSupported();

            \boolval($configFile[Settings::KEY_IS_FORCE_ADD_METHODS] ?? false) === true ?
                $settings->enableAddAllowedMethodsToPreFlightResponse() :
                $settings->disableAddAllowedMethodsToPreFlightResponse();

            \boolval($configFile[Settings::KEY_IS_FORCE_ADD_HEADERS] ?? false) === true ?
                $settings->enableAddAllowedHeadersToPreFlightResponse() :
                $settings->disableAddAllowedHeadersToPreFlightResponse();

            \boolval($configFile[Settings::KEY_IS_CHECK_HOST] ?? false) === true ?
                $settings->enableCheckHost() : $settings->disableCheckHost();


            \boolval($configFile[Settings::KEY_LOGS_ENABLED] ?? false) === true ?
                $settings->enableLog() : $settings->disableLog();

            $this->settingsData = $settings->getData();
        }

        return $this->settingsData;
    }

    /**
     * @return Repository
     */
    protected function getConfigRepository()
    {
        /** @var Repository $config */
        $config = $this->app['config'];

        return $config;
    }

    /**
     * @return array
     */
    protected function getBaseConfig(): array
    {
        $path = $this->getConfigPath();
        /** @noinspection PhpIncludeInspection */
        $base = require $path;

        return $base;
    }
}
