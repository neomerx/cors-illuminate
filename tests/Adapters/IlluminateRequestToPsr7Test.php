<?php declare(strict_types = 1);

namespace Neomerx\Tests\CorsIlluminate\Adapters;

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
use Mockery;
use Neomerx\CorsIlluminate\Adapters\IlluminateRequestToPsr7;
use Neomerx\Tests\CorsIlluminate\BaseTestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @package Neomerx\Tests\CorsIlluminate
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class IlluminateRequestToPsr7Test extends BaseTestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var RequestInterface
     */
    private $adapter;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new Request();
        $this->adapter = new IlluminateRequestToPsr7($this->request);
    }

    /**
     * Test get method.
     */
    public function testGetMethod()
    {
        $this->request->setMethod('XXX');
        $this->assertEquals('XXX', $this->adapter->getMethod());
    }

    /**
     * Test has header.
     */
    public function testHasHeader()
    {
        $name = 'Some-Header';

        $this->assertFalse($this->adapter->hasHeader($name));

        $this->request->headers->add([$name => 'some value']);
        $this->assertTrue($this->adapter->hasHeader($name));
    }

    /**
     * Test get header.
     */
    public function testGetHeader()
    {
        $name = 'Some-Header';

        $this->assertEquals([], $this->adapter->getHeader($name));

        $this->request->headers->add([$name => 'value 1']);
        $this->assertEquals(['value 1'], $this->adapter->getHeader($name));

        $this->request->headers->set($name, ['value 2'], false);
        $this->assertEquals(['value 1', 'value 2'], $this->adapter->getHeader($name));
    }

    /**
     * @return void
     */
    public function testGetProtocolVersion(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->getProtocolVersion();
    }

    /**
     * @return void
     */
    public function testWithProtocolVersion(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->withProtocolVersion(null);
    }

    /**
     * @return void
     */
    public function testGetHeaders(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->getHeaders();
    }

    /**
     * @return void
     */
    public function testGetHeaderLine(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->getHeaderLine(null);
    }

    /**
     * @return void
     */
    public function testWithHeader(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->withHeader(null, null);
    }

    /**
     * @return void
     */
    public function testWithAddedHeader(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->withAddedHeader(null, null);
    }

    /**
     * @return void
     */
    public function testWithoutHeader(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->withoutHeader(null);
    }

    /**
     * @return void
     */
    public function testGetBody(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->getBody();
    }

    /**
     * @return void
     */
    public function testWithBody(): void
    {
        $this->expectException(\LogicException::class);

        /** @var StreamInterface $body */
        $body = Mockery::mock(StreamInterface::class);
        $this->adapter->withBody($body);
    }

    /**
     * @return void
     */
    public function testGetRequestTarget(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->getRequestTarget();
    }

    /**
     * @return void
     */
    public function testWithRequestTarget(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->withRequestTarget(null);
    }

    /**
     * @return void
     */
    public function testWithMethod(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->withMethod(null);
    }

    /**
     * @return void
     */
    public function testGetUri(): void
    {
        $this->expectException(\LogicException::class);

        $this->adapter->getUri();
    }

    /**
     * @return void
     */
    public function testWithUri(): void
    {
        $this->expectException(\LogicException::class);

        /** @var UriInterface $uri */
        $uri = Mockery::mock(UriInterface::class);
        $this->adapter->withUri($uri);
    }
}
