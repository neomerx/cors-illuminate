<?php namespace Neomerx\Tests\CorsIlluminate\Settings;

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
        $originValue = Settings::$isUsingCredentials;
        try {
            $newValue = !$originValue;
            Settings::setSettings([Settings::KEY_IS_USING_CREDENTIALS => $newValue]);
            $this->assertEquals($newValue, Settings::$isUsingCredentials);
        } finally {
            Settings::$isUsingCredentials = $originValue;
        }
    }
}
