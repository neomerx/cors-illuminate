<?php namespace Neomerx\Tests\CorsIlluminate;

/**
 * Copyright 2015-2019 info@neomerx.com
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

use \Closure;
use \Mockery;
use \Mockery\MockInterface;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\CorsIlluminate\CorsMiddleware;
use \Neomerx\Cors\Contracts\AnalyzerInterface;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;
use \Neomerx\Cors\Contracts\Constants\CorsResponseHeaders;
use \Illuminate\Contracts\Container\Container as ContainerInterface;

/**
 * @package Neomerx\Tests\CorsIlluminate
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CorsMiddlewareTest extends BaseTestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @var AnalysisResultInterface
     */
    private $analysisResult;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CorsMiddleware
     */
    private $middleware;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->request        = Mockery::mock(Request::class);
        $this->analyzer       = Mockery::mock(AnalyzerInterface::class);
        $this->container      = Mockery::mock(ContainerInterface::class);
        $this->analysisResult = Mockery::mock(AnalysisResultInterface::class);
        $this->middleware     = new CorsMiddleware($this->analyzer, $this->container);
    }

    /**
     * Test middleware handling.
     */
    public function testOutOfScope()
    {
        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled);
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockContainerCall('instance', [AnalysisResultInterface::class, $this->analysisResult]);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE);

        $this->assertNotNull($this->middleware->handle($this->request, $next));
        $this->assertTrue($nextCalled);
    }

    /**
     * Test middleware handling.
     */
    public function testPreFlight()
    {
        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled);
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockContainerCall('instance', [AnalysisResultInterface::class, $this->analysisResult]);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST);
        $this->mockAnalysisCall('getResponseHeaders', []);

        $this->assertNotNull($this->middleware->handle($this->request, $next));
        $this->assertFalse($nextCalled);
    }

    /**
     * Test middleware handling.
     */
    public function testActualCors()
    {
        $headerName = 'Some-Header';

        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled, new Response(null, Response::HTTP_OK, [$headerName => 'value 1']));
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockContainerCall('instance', [AnalysisResultInterface::class, $this->analysisResult]);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::TYPE_ACTUAL_REQUEST);
        $this->mockAnalysisCall(
            'getResponseHeaders',
            [
                $headerName => 'value 2',
                CorsResponseHeaders::EXPOSE_HEADERS => ['expose1', 'expose2'],
            ]
        );

        /** @var Response $response */
        $this->assertNotNull($response = $this->middleware->handle($this->request, $next));
        $this->assertTrue($nextCalled);
        $this->assertEquals(['value 1', 'value 2'], $response->headers->get($headerName, null, false));
        $this->assertEquals('expose1, expose2', $response->headers->get(CorsResponseHeaders::EXPOSE_HEADERS));
    }

    /**
     * Test middleware handling.
     */
    public function testError()
    {
        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled);
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockContainerCall('instance', [AnalysisResultInterface::class, $this->analysisResult]);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED);

        $this->assertNotNull($this->middleware->handle($this->request, $next));
        $this->assertFalse($nextCalled);
    }

    /**
     * @param bool &$nextCalled
     * @param mixed $response
     *
     * @return Closure
     */
    private function getMockForNext(&$nextCalled, $response = 'some response')
    {
        $next = function () use (&$nextCalled, $response) {
            $nextCalled = true;

            return $response;
        };

        return $next;
    }

    /**
     * @param string $method
     * @param mixed  $returnValue
     *
     * @return void
     */
    private function mockAnalyzerCall($method, $returnValue)
    {
        /** @var MockInterface $analyzer */
        $analyzer = $this->analyzer;
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $analyzer->shouldReceive($method)->once()->withAnyArgs()->andReturn($returnValue);
    }

    /**
     * @param string $method
     * @param array  $args
     * @param mixed  $result
     *
     * @return void
     */
    private function mockContainerCall($method, array $args, $result = null)
    {
        /** @var MockInterface $container */
        $container = $this->container;
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $container->shouldReceive($method)->once()->withArgs($args)->andReturn($result);
    }

    /**
     * @param string $method
     * @param mixed  $returnValue
     *
     * @return void
     */
    private function mockAnalysisCall($method, $returnValue)
    {
        /** @var MockInterface $analysisResult */
        $analysisResult = $this->analysisResult;
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $analysisResult->shouldReceive($method)->once()->withAnyArgs()->andReturn($returnValue);
    }
}
