<?php namespace Neomerx\Tests\CorsIlluminate;

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

use \Closure;
use \Mockery;
use \Mockery\MockInterface;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\CorsIlluminate\CorsMiddleware;
use \Neomerx\Cors\Contracts\AnalyzerInterface;
use \Neomerx\CorsIlluminate\Settings\Settings;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;

/**
 * @package Neomerx\Tests\CorsIlluminate
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
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->request        = Mockery::mock(Request::class);
        $this->analyzer       = Mockery::mock(AnalyzerInterface::class);
        $this->analysisResult = Mockery::mock(AnalysisResultInterface::class);
    }

    /**
     * Test can be created without input args.
     */
    public function testCreate()
    {
        $middleware = new CorsMiddleware();

        $oldOrigin  = Settings::$serverOrigin;
        $oldAllowed = Settings::$allowedOrigins;
        try {
            Settings::$serverOrigin   = [
                'scheme' => 'http',
                'host'   => 'localhost',
                'port'   => 8080,
            ];
            Settings::$allowedOrigins = [];

            $middleware->handle(new Request(), function () {
                return null;
            });
        } finally {
            Settings::$serverOrigin = $oldOrigin;
            Settings::$allowedOrigins = $oldAllowed;
        }
    }

    /**
     * Test middleware handling.
     */
    public function testOutOfScope()
    {
        $middleware = new CorsMiddleware($this->analyzer);
        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled);
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE);

        $this->assertNotNull($middleware->handle($this->request, $next));
        $this->assertTrue($nextCalled);
    }

    /**
     * Test middleware handling.
     */
    public function testPreFlight()
    {
        $middleware = new CorsMiddleware($this->analyzer);
        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled);
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST);
        $this->mockAnalysisCall('getResponseHeaders', []);

        $this->assertNotNull($middleware->handle($this->request, $next));
        $this->assertFalse($nextCalled);
    }

    /**
     * Test middleware handling.
     */
    public function testActualCors()
    {
        $headerName = 'Some-Header';

        $middleware = new CorsMiddleware($this->analyzer);
        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled, new Response(null, Response::HTTP_OK, [$headerName => 'value 1']));
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::TYPE_ACTUAL_REQUEST);
        $this->mockAnalysisCall('getResponseHeaders', [$headerName => 'value 2']);

        /** @var Response $response */
        $this->assertNotNull($response = $middleware->handle($this->request, $next));
        $this->assertTrue($nextCalled);
        $this->assertEquals(['value 1', 'value 2'], $response->headers->get($headerName, null, false));
    }

    /**
     * Test middleware handling.
     */
    public function testError()
    {
        $middleware = new CorsMiddleware($this->analyzer);
        $nextCalled = false;
        $next = $this->getMockForNext($nextCalled);
        $this->mockAnalyzerCall('analyze', $this->analysisResult);
        $this->mockAnalysisCall('getRequestType', AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED);

        $this->assertNotNull($middleware->handle($this->request, $next));
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
