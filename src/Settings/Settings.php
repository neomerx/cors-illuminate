<?php namespace Neomerx\CorsIlluminate\Settings;

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

use \Neomerx\Cors\Strategies\Settings as CorsSettings;

/**
 * @package Neomerx\CorsIlluminate
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Settings extends CorsSettings
{
    /**
     * If CORS handling should be logged (true/false, by default is turned off).
     */
    const KEY_LOGS_ENABLED = 21;

    /**
     * @return bool
     */
    public function isLogsEnabled()
    {
        return array_key_exists(self::KEY_LOGS_ENABLED, $this->settings) === true ?
            $this->settings[self::KEY_LOGS_ENABLED] : false;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setLogsEnabled($enabled)
    {
        $this->settings[self::KEY_LOGS_ENABLED] = $enabled;

        return $this;
    }
}
