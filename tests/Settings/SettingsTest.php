<?php namespace Neomerx\Tests\CorsIlluminate\Settings;

/**
 * Copyright 2015-2017 info@neomerx.com
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

use \Neomerx\CorsIlluminate\Settings\Settings;
use \Neomerx\Tests\CorsIlluminate\BaseTestCase;

/**
 * @package Neomerx\Tests\CorsIlluminate
 */
class SettingsTest extends BaseTestCase
{
    /**
     * Test set settings.
     */
    public function testSetSettings()
    {
        $origin = [
            'scheme' => 'http',
            'host'   => 'some-host.com',
            'port'   => 567,
        ];

        $settings = new Settings([
            Settings::KEY_SERVER_ORIGIN        => $origin,
            Settings::KEY_ALLOWED_ORIGINS      => ['http://does-not-matter.foo' => true],
            Settings::KEY_ALLOWED_METHODS      => ['DOES-NOT-MATTER'   => true],
            Settings::KEY_ALLOWED_HEADERS      => ['x-does-not-matter' => true],
            Settings::KEY_EXPOSED_HEADERS      => ['x-does-not-matter' => true],
            Settings::KEY_IS_USING_CREDENTIALS => false,
            Settings::KEY_FLIGHT_CACHE_MAX_AGE => 0,
            Settings::KEY_IS_FORCE_ADD_METHODS => false,
            Settings::KEY_IS_FORCE_ADD_HEADERS => false,
            Settings::KEY_IS_CHECK_HOST        => false,
            Settings::KEY_LOGS_ENABLED         => true,
        ]);

        $this->assertEquals($origin, $settings->getServerOrigin());
        $this->assertEquals(false, $settings->isCheckHost());
        $this->assertEquals(true, $settings->isLogsEnabled());

        $settings->setLogsEnabled(false);

        $this->assertEquals(false, $settings->isLogsEnabled());
    }
}
