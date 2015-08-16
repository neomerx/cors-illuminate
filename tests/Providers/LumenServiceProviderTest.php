<?php namespace Neomerx\Tests\CorsIlluminate\Providers;

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

use \Mockery;
use \ArrayAccess;
use \ReflectionClass;
use \ReflectionMethod;
use \Mockery\MockInterface;
use \Neomerx\Tests\CorsIlluminate\BaseTestCase;
use \Neomerx\CorsIlluminate\Providers\LumenServiceProvider;
use \Illuminate\Contracts\Foundation\Application as ApplicationInterface;

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
    protected function setUp()
    {
        parent::setUp();

        $this->app = Mockery::mock(ArrayAccess::class);
        $this->provider = new LumenServiceProvider($this->app);
    }

    /**
     * Test configureCorsAnalyzer method.
     */
    public function testConfigureCorsAnalyzer()
    {
        $method = $this->getMethod('configureCorsAnalyzer');

        /** @var MockInterface $app */
        $app = $this->app;
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('configure')->once()->withAnyArgs()->andReturnUndefined();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('bind')->withAnyArgs()->twice()->andReturnUndefined();

        $method->invokeArgs($this->provider, []);
    }

    /**
     * Test registerPublishConfig method.
     */
    public function testRegisterPublishConfig()
    {
        $method = $this->getMethod('registerPublishConfig');
        $method->invokeArgs($this->provider, []);
    }

    /**
     * @param string $name
     *
     * @return ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class  = new ReflectionClass(LumenServiceProvider::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
