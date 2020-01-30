<?php declare(strict_types = 1);

namespace Neomerx\Tests\CorsIlluminate\Providers;

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

use ArrayAccess;
use Closure;
use Illuminate\Contracts\Foundation\Application as ApplicationInterface;
use Mockery;
use Mockery\MockInterface;
use Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Neomerx\CorsIlluminate\Providers\LaravelServiceProvider;
use Neomerx\CorsIlluminate\Settings\Settings;
use Neomerx\Tests\CorsIlluminate\BaseTestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

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
    protected function setUp(): void
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
     *
     * @return void
     */
    public function testRegister(): void
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->config->shouldReceive('get')->withAnyArgs()->once()->andReturn([]);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->config->shouldReceive('set')->withAnyArgs()->once()->andReturnUndefined();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->app->shouldReceive('bind')->withAnyArgs()->twice()->andReturnUndefined();

        $this->provider->register();

        // mocks will do the checks
        $this->assertTrue(true);
    }

    /**
     * Test boot provider.
     *
     * @return void
     */
    public function testBoot(): void
    {
        $this->provider->boot();

        // mocks will do the checks
        $this->assertTrue(true);
    }

    /**
     * Test create analysis strategy.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testGetCreateAnalysisStrategyClosure1(): void
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
                Settings::KEY_SERVER_ORIGIN => ['scheme' => 'http', 'host' => 'localhost', 'port' => 80],
            ]);

        $this->assertInstanceOf(Closure::class, $closure);
        $this->assertNotNull($strategy = $closure($app));
        $this->assertInstanceOf(AnalysisStrategyInterface::class, $strategy);

        /** @var AnalysisStrategyInterface $strategy */

        // as there were no origins section in the config then all origins should be allowed
        $this->assertTrue($strategy->isRequestOriginAllowed('http://any-url.sample'));
    }

    /**
     * Test create analysis strategy.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testGetCreateAnalysisStrategyClosure2(): void
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
                Settings::KEY_SERVER_ORIGIN        => ['scheme' => 'http', 'host' => 'localhost', 'port' => 80],
                Settings::KEY_ALLOWED_ORIGINS      => ['http://allowed-url.sample'],
                Settings::KEY_IS_FORCE_ADD_METHODS => true,
                Settings::KEY_IS_FORCE_ADD_HEADERS => true,
            ]);

        $this->assertInstanceOf(Closure::class, $closure);
        $this->assertNotNull($strategy = $closure($app));
        $this->assertInstanceOf(AnalysisStrategyInterface::class, $strategy);


        // as there was origins section in the config then origins check should work
        $this->assertTrue($strategy->isRequestOriginAllowed('http://allowed-url.sample'));
        $this->assertFalse($strategy->isRequestOriginAllowed('http://not-allowed-url.sample'));
    }

    /**
     * Test create analyzer.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testGetCreateAnalyzerClosure(): void
    {
        $method     = self::getMethod('getCreateAnalyzerClosure');
        $logger     = Mockery::mock(LoggerInterface::class);
        $app        = [
            'config'                         => $this->config,
            AnalysisStrategyInterface::class => $this->strategy,
            LoggerInterface::class           => $logger,
        ];


        /** @var MockInterface $mockStrategy */
        $mockStrategy = $this->strategy;

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $mockStrategy
            ->shouldReceive('isLogEnabled')
            ->withNoArgs()
            ->once()
            ->andReturn(true);
        $mockStrategy
            ->shouldReceive('setLogger')
            ->once()
            ->andReturnUndefined();

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
    protected static function getMethod(string $name): ReflectionMethod
    {
        $class  = new ReflectionClass(LaravelServiceProvider::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
