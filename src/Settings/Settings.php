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
    const KEY_LOGS_ENABLED = 'logsEnabled';

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
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        if (empty($settings) === false) {
            $this->setSettings($settings);
        }
    }

    /**
     * Set app CORS settings.
     *
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->configServerOrigin($settings);
        $this->configRequestAllowedOrigins($settings);
        $this->configRequestAllowedMethods($settings);
        $this->configRequestAllowedHeaders($settings);
        $this->configResponseExposedHeaders($settings);
        $this->configRequestCredentialsSupported($settings);
        $this->configPreFlightCacheMaxAge($settings);
        $this->configForceAddAllowedMethodsToPreFlightResponse($settings);
        $this->configForceAddAllowedHeadersToPreFlightResponse($settings);
        $this->configCheckHost($settings);
    }

    /**
     * @param array $settings
     */
    private function configServerOrigin($settings)
    {
        array_key_exists(self::KEY_SERVER_ORIGIN, $settings) === false ?:
            $this->setServerOrigin($settings[self::KEY_SERVER_ORIGIN]);
    }

    /**
     * @param array $settings
     */
    private function configRequestAllowedOrigins($settings)
    {
        array_key_exists(self::KEY_ALLOWED_ORIGINS, $settings) === false ?:
            $this->setRequestAllowedOrigins($settings[self::KEY_ALLOWED_ORIGINS]);
    }

    /**
     * @param array $settings
     */
    private function configRequestAllowedMethods($settings)
    {
        array_key_exists(self::KEY_ALLOWED_METHODS, $settings) === false ?:
            $this->setRequestAllowedMethods($settings[self::KEY_ALLOWED_METHODS]);
    }

    /**
     * @param array $settings
     */
    private function configRequestAllowedHeaders($settings)
    {
        array_key_exists(self::KEY_ALLOWED_HEADERS, $settings) === false ?:
            $this->setRequestAllowedHeaders($settings[self::KEY_ALLOWED_HEADERS]);
    }

    /**
     * @param array $settings
     */
    private function configResponseExposedHeaders($settings)
    {
        array_key_exists(self::KEY_EXPOSED_HEADERS, $settings) === false ?:
            $this->setResponseExposedHeaders($settings[self::KEY_EXPOSED_HEADERS]);
    }

    /**
     * @param array $settings
     */
    private function configRequestCredentialsSupported($settings)
    {
        array_key_exists(self::KEY_IS_USING_CREDENTIALS, $settings) === false ?:
            $this->setRequestCredentialsSupported($settings[self::KEY_IS_USING_CREDENTIALS]);
    }

    /**
     * @param array $settings
     */
    private function configPreFlightCacheMaxAge($settings)
    {
        array_key_exists(self::KEY_PRE_FLIGHT_MAX_AGE, $settings) === false ?:
            $this->setPreFlightCacheMaxAge($settings[self::KEY_PRE_FLIGHT_MAX_AGE]);
    }

    /**
     * @param array $settings
     */
    private function configForceAddAllowedMethodsToPreFlightResponse($settings)
    {
        array_key_exists(self::KEY_FORCE_ADD_METHODS, $settings) === false ?:
            $this->setForceAddAllowedMethodsToPreFlightResponse($settings[self::KEY_FORCE_ADD_METHODS]);
    }

    /**
     * @param array $settings
     */
    private function configForceAddAllowedHeadersToPreFlightResponse($settings)
    {
        array_key_exists(self::KEY_FORCE_ADD_HEADERS, $settings) === false ?:
            $this->setForceAddAllowedHeadersToPreFlightResponse($settings[self::KEY_FORCE_ADD_HEADERS]);
    }

    /**
     * @param array $settings
     */
    private function configCheckHost($settings)
    {
        array_key_exists(self::KEY_CHECK_HOST_HEADER, $settings) === false ?:
            $this->setCheckHost($settings[self::KEY_CHECK_HOST_HEADER]);
    }
}
