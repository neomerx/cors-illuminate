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
     * Test register provider.
     */
    public function testRegister()
    {
        /** @var MockInterface $app */
        $app = $this->app;
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('offsetGet')->once()->withAnyArgs()->andReturnNull();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('configure')->once()->withAnyArgs()->andReturnUndefined();

        $this->provider->boot();
    }
}
