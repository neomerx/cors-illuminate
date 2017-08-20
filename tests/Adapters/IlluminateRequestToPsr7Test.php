<?php namespace Neomerx\Tests\CorsIlluminate\Adapters;

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

use \Mockery;
use \Illuminate\Http\Request;
use \Psr\Http\Message\UriInterface;
use \Psr\Http\Message\StreamInterface;
use \Psr\Http\Message\RequestInterface;
use \Neomerx\Tests\CorsIlluminate\BaseTestCase;
use \Neomerx\CorsIlluminate\Adapters\IlluminateRequestToPsr7;

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
    protected function setUp()
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
     * @expectedException \LogicException
     */
    public function testGetProtocolVersion()
    {
        $this->adapter->getProtocolVersion();
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithProtocolVersion()
    {
        $this->adapter->withProtocolVersion(null);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetHeaders()
    {
        $this->adapter->getHeaders();
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetHeaderLine()
    {
        $this->adapter->getHeaderLine(null);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithHeader()
    {
        $this->adapter->withHeader(null, null);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithAddedHeader()
    {
        $this->adapter->withAddedHeader(null, null);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithoutHeader()
    {
        $this->adapter->withoutHeader(null);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetBody()
    {
        $this->adapter->getBody();
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithBody()
    {
        /** @var StreamInterface $body */
        $body = Mockery::mock(StreamInterface::class);
        $this->adapter->withBody($body);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetRequestTarget()
    {
        $this->adapter->getRequestTarget();
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithRequestTarget()
    {
        $this->adapter->withRequestTarget(null);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithMethod()
    {
        $this->adapter->withMethod(null);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetUri()
    {
        $this->adapter->getUri();
    }

    /**
     * @expectedException \LogicException
     */
    public function testWithUri()
    {
        /** @var UriInterface $uri */
        $uri = Mockery::mock(UriInterface::class);
        $this->adapter->withUri($uri);
    }
}
