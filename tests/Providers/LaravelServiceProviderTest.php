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
use \Mockery\MockInterface;
use \Neomerx\Tests\CorsIlluminate\BaseTestCase;
use \Neomerx\CorsIlluminate\Providers\LaravelServiceProvider;
use \Illuminate\Contracts\Foundation\Application as ApplicationInterface;

/**
 * @package Neomerx\Tests\CorsIlluminate
 */
class LaravelServiceProviderTest extends BaseTestCase
{
    /**
     * @var ApplicationInterface
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
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->config = Mockery::mock();
        $this->app    = [
            'path.config' => '/some/config/path',
            'config'      => $this->config,
        ];

        $this->provider = new LaravelServiceProvider($this->app);
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

        $this->provider->register();
    }

    /**
     * Test boot provider.
     */
    public function testBoot()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->config->shouldReceive('get')->withAnyArgs()->once()->andReturn([]);

        $this->provider->boot();
    }
}
