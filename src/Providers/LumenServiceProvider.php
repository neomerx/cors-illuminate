<?php declare(strict_types = 1);

namespace Neomerx\CorsIlluminate\Providers;

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

/**
 * @package Neomerx\CorsIlluminate
 */
class LumenServiceProvider extends LaravelServiceProvider
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function registerPublishConfig(): void
    {
        // do nothing
    }

    /**
     * @inheritdoc
     */
    protected function configureCorsAnalyzer(): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->app->configure(self::CONFIG_FILE_NAME_WO_EXT);

        parent::configureCorsAnalyzer();
    }
}
