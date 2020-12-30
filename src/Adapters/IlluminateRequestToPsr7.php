<?php declare(strict_types = 1);

namespace Neomerx\CorsIlluminate\Adapters;

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

use Illuminate\Http\Request;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * This class is a wrapper for Laravel/Lumen Requests to PSR-7 compatible objects designed specifically for
 * neomerx/cors-psr-7 package and implements only the methods required by neomerx/cors-psr-7.
 *
 * If you are already using PSR-7 Bridge solutions it's totally fine to replace this class with them.
 * The main benefit of this class it's very lightweight.
 *
 * @package Neomerx\CorsIlluminate
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class IlluminateRequestToPsr7 implements RequestInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name)
    {
        return $this->request->headers->has($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        return $this->request->headers->all($name);
    }

    /**
     * @inheritdoc
     */
    public function getProtocolVersion()
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getRequestTarget()
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function withMethod($method)
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
        throw new LogicException('Method is not implemented');
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        throw new LogicException('Method is not implemented');
    }
}
