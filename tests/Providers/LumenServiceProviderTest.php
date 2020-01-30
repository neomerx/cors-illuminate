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
use Illuminate\Contracts\Foundation\Application as ApplicationInterface;
use Mockery;
use Mockery\MockInterface;
use Neomerx\CorsIlluminate\Providers\LumenServiceProvider;
use Neomerx\Tests\CorsIlluminate\BaseTestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * @package Neomerx\Tests\CorsIlluminate
 */
class LumenServiceProviderTest extends BaseTestCase
{
    /**
     * @var ApplicationInterface
     */
    private $app;

    /**
     * @var LumenServiceProvider
     */
    private $provider;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app = Mockery::mock(ArrayAccess::class);
        $this->provider = new LumenServiceProvider($this->app);
    }

    /**
     * Test configureCorsAnalyzer method.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testConfigureCorsAnalyzer(): void
    {
        $method = $this->getMethod('configureCorsAnalyzer');

        /** @var MockInterface $app */
        $app = $this->app;
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('configure')->once()->withAnyArgs()->andReturnUndefined();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('bind')->withAnyArgs()->twice()->andReturnUndefined();

        $method->invokeArgs($this->provider, []);

        // mocks will do the checks
        $this->assertTrue(true);
    }

    /**
     * Test registerPublishConfig method.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testRegisterPublishConfig(): void
    {
        $method = $this->getMethod('registerPublishConfig');
        $method->invokeArgs($this->provider, []);

        // mocks will do the checks
        $this->assertTrue(true);
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
        $class  = new ReflectionClass(LumenServiceProvider::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
