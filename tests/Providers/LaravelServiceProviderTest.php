<?php namespace Neomerx\Tests\CorsIlluminate\Providers;

/**
 * Copyright 2015-2019 info@neomerx.com
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
use \Mockery;
use \ArrayAccess;
use \ReflectionClass;
use ReflectionException;
use \ReflectionMethod;
use \Mockery\MockInterface;
use \Neomerx\Cors\Contracts\AnalyzerInterface;
use \Neomerx\CorsIlluminate\Settings\Settings;
use \Neomerx\Tests\CorsIlluminate\BaseTestCase;
use \Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use \Neomerx\CorsIlluminate\Providers\LaravelServiceProvider;
use \Illuminate\Contracts\Foundation\Application as ApplicationInterface;

/**
 * @package Neomerx\Tests\CorsIlluminate
 */
class LaravelServiceProviderTest extends BaseTestCase
{
    /**
     * @var MockInterface
     */
    private $app;

    /**
     * @var MockInterface
     */
    private $config;

    /**
     * @var LaravelServiceProvider
     */
    private $provider;

    /**
     * @var AnalysisStrategyInterface
     */
    private $strategy;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->config   = Mockery::mock();
        $this->strategy = Mockery::mock(AnalysisStrategyInterface::class);

        $this->app = Mockery::mock(ArrayAccess::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('path.config')->andReturn('/some/config/path');
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('config')->andReturn($this->config);

        /** @var ApplicationInterface $app */
        $app = $this->app;

        $this->provider = new LaravelServiceProvider($app);
    }

    /**
     * Test register provider.
     */
    public function testRegister()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->config->shouldReceive('get')->withAnyArgs()->once()->andReturn([]);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->config->shouldReceive('set')->withAnyArgs()->once()->andReturnUndefined();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->app->shouldReceive('bind')->withAnyArgs()->twice()->andReturnUndefined();

        $this->provider->register();
    }

    /**
     * Test boot provider.
     */
    public function testBoot()
    {
        $this->provider->boot();
    }

    /**
     * Test create analysis strategy.
     *
     * @throws ReflectionException
     */
    public function testGetCreateAnalysisStrategyClosure()
    {
        $method  = self::getMethod('getCreateAnalysisStrategyClosure');
        $app     = [
            'config' => $this->config,
        ];

        /** @var Closure $closure */
        $closure = $method->invokeArgs($this->provider, []);

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->config
            ->shouldReceive('get')
            ->withArgs([LaravelServiceProvider::CONFIG_FILE_NAME_WO_EXT, []])
            ->once()
            ->andReturn([
                Settings::KEY_SERVER_ORIGIN => 'http://localhost',
            ]);

        $this->assertInstanceOf(Closure::class, $closure);
        $this->assertNotNull($strategy = $closure($app));
        $this->assertInstanceOf(AnalysisStrategyInterface::class, $strategy);
    }

    /**
     * Test create analyzer.
     *
     * @throws ReflectionException
     */
    public function testGetCreateAnalyzerClosure()
    {
        $method  = self::getMethod('getCreateAnalyzerClosure');
        $app     = [
            'config'                         => $this->config,
            AnalysisStrategyInterface::class => $this->strategy,
        ];

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->config
            ->shouldReceive('get')
            ->withArgs([LaravelServiceProvider::CONFIG_FILE_NAME_WO_EXT, []])
            ->once()
            ->andReturn([
                Settings::KEY_SERVER_ORIGIN => 'http://localhost',
            ]);

        /** @var Closure $closure */
        $closure = $method->invokeArgs($this->provider, []);

        $this->assertInstanceOf(Closure::class, $closure);
        $this->assertNotNull($analyzer = $closure($app));
        $this->assertInstanceOf(AnalyzerInterface::class, $analyzer);
    }

    /**
     * @param string $name
     *
     * @return ReflectionMethod
     *
     * @throws ReflectionException
     */
    protected static function getMethod($name)
    {
        $class  = new ReflectionClass(LaravelServiceProvider::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
