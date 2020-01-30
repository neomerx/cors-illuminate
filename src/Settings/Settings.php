<?php declare(strict_types = 1);

namespace Neomerx\CorsIlluminate\Settings;

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

use Neomerx\Cors\Strategies\Settings as CorsSettings;

/**
 * @package Neomerx\CorsIlluminate
 */
class Settings extends CorsSettings
{
    /** @var int Settings key */
    public const KEY_SERVER_ORIGIN = 0;

    /** @var int Settings key */
    public const KEY_ALLOWED_ORIGINS = self::KEY_SERVER_ORIGIN + 1;

    /** @var int Settings key */
    public const KEY_ALLOWED_METHODS = self::KEY_ALLOWED_ORIGINS + 1;

    /** @var int Settings key */
    public const KEY_ALLOWED_HEADERS = self::KEY_ALLOWED_METHODS + 1;

    /** @var int Settings key */
    public const KEY_EXPOSED_HEADERS = self::KEY_ALLOWED_HEADERS + 1;

    /** @var int Settings key */
    public const KEY_IS_USING_CREDENTIALS = self::KEY_EXPOSED_HEADERS + 1;

    /** @var int Settings key */
    public const KEY_FLIGHT_CACHE_MAX_AGE = self::KEY_IS_USING_CREDENTIALS + 1;

    /** @var int Settings key */
    public const KEY_IS_FORCE_ADD_METHODS = self::KEY_FLIGHT_CACHE_MAX_AGE + 1;

    /** @var int Settings key */
    public const KEY_IS_FORCE_ADD_HEADERS = self::KEY_IS_FORCE_ADD_METHODS + 1;

    /** @var int Settings key */
    public const KEY_IS_CHECK_HOST = self::KEY_IS_FORCE_ADD_HEADERS + 1;

    /** @var int Settings key */
    public const KEY_LOGS_ENABLED = self::KEY_IS_CHECK_HOST + 1;

    /** @var int Cached settings key */
    protected const CORS_ILLUMINATE_SETTINGS_CACHE_KEY_IS_LOG_ENABLED = 25; // more than the base class has properties.

    /** @var int Cached settings key */
    protected const CORS_ILLUMINATE_SETTINGS_CACHE_KEY_LAST = self::CORS_ILLUMINATE_SETTINGS_CACHE_KEY_IS_LOG_ENABLED;

    /** @return bool */
    private const DEFAULT_IS_LOG_ENABLED = false;

    /** @var bool If logging is enabled */
    private $isLogEnabled = self::DEFAULT_IS_LOG_ENABLED;

    /**
     * @return bool
     */
    public function isLogEnabled(): bool
    {
        return $this->isLogEnabled;
    }

    /**
     * @return self
     */
    public function enableLog(): self
    {
        $this->isLogEnabled = true;

        return $this;
    }

    /**
     * @return self
     */
    public function disableLog(): self
    {
        $this->isLogEnabled = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $data = parent::getData();

        // check we won't override any parent properties
        \assert(\array_key_exists(static::CORS_ILLUMINATE_SETTINGS_CACHE_KEY_IS_LOG_ENABLED, $data) === false);

        $data[static::CORS_ILLUMINATE_SETTINGS_CACHE_KEY_IS_LOG_ENABLED] = $this->isLogEnabled();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data): CorsSettings
    {
        $isLogEnabled = \boolval(
            $data[static::CORS_ILLUMINATE_SETTINGS_CACHE_KEY_IS_LOG_ENABLED] ?? static::DEFAULT_IS_LOG_ENABLED
        );
        $isLogEnabled === true ? $this->enableLog() : $this->disableLog();

        return parent::setData($data);
    }
}
