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
 */
class Settings extends CorsSettings
{
    /**
     * Could be string or array.
     */
    const KEY_SERVER_ORIGIN = 'serverOrigin';

    /**
     * A list of allowed request origins (lower-cased, no trail slashes).
     */
    const KEY_ALLOWED_ORIGINS = 'origins';

    /**
     * A list of allowed request methods (case sensitive).
     */
    const KEY_ALLOWED_METHODS = 'allowedMethods';

    /**
     * A list of allowed request headers (lower-cased).
     */
    const KEY_ALLOWED_HEADERS = 'allowedHeaders';

    /**
     * A list of headers (case insensitive) which will be made accessible to user agent (browser) in response.
     */
    const KEY_EXPOSED_HEADERS = 'exposedHeaders';

    /**
     * If access with credentials is supported by the resource.
     */
    const KEY_IS_USING_CREDENTIALS = 'supportsCredentials';

    /**
     * Pre-flight response cache max period in seconds.
     */
    const KEY_PRE_FLIGHT_MAX_AGE = 'maxAge';

    /**
     * If allowed methods should be added to pre-flight response when 'simple' method is requested.
     */
    const KEY_FORCE_ADD_METHODS = 'forceAddMethods';

    /**
     * If allowed headers should be added when request headers are 'simple' and
     * non of them is 'Content-Type'.
     */
    const KEY_FORCE_ADD_HEADERS = 'forceAddHeaders';

    /**
     * If request 'Host' header should be checked against server's origin.
     */
    const KEY_CHECK_HOST_HEADER = 'checkHost';

    /**
     * Set app CORS settings.
     *
     * @param array $settings
     */
    public static function setSettings(array $settings)
    {
        self::$serverOrigin   = self::getSettingsValue($settings, self::KEY_SERVER_ORIGIN, self::$serverOrigin);
        self::$allowedOrigins = self::getSettingsValue($settings, self::KEY_ALLOWED_ORIGINS, self::$allowedOrigins);
        self::$allowedMethods = self::getSettingsValue($settings, self::KEY_ALLOWED_METHODS, self::$allowedMethods);
        self::$allowedHeaders = self::getSettingsValue($settings, self::KEY_ALLOWED_HEADERS, self::$allowedHeaders);
        self::$exposedHeaders = self::getSettingsValue($settings, self::KEY_EXPOSED_HEADERS, self::$exposedHeaders);
        self::$isCheckHost    = self::getSettingsValue($settings, self::KEY_CHECK_HOST_HEADER, self::$isCheckHost);
        self::$isUsingCredentials =
            self::getSettingsValue($settings, self::KEY_IS_USING_CREDENTIALS, self::$isUsingCredentials);
        self::$preFlightCacheMaxAge =
            self::getSettingsValue($settings, self::KEY_PRE_FLIGHT_MAX_AGE, self::$preFlightCacheMaxAge);
        self::$isForceAddMethods =
            self::getSettingsValue($settings, self::KEY_FORCE_ADD_METHODS, self::$isForceAddMethods);
        self::$isForceAddHeaders =
            self::getSettingsValue($settings, self::KEY_FORCE_ADD_HEADERS, self::$isForceAddHeaders);
    }

    /**
     * @param array  $settings
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    private static function getSettingsValue(array $settings, $key, $default)
    {
        return isset($settings[$key]) === true ? $settings[$key] : $default;
    }
}
