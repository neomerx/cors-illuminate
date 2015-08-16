<?php namespace Neomerx\CorsIlluminate;

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
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Psr\Http\Message\RequestInterface;
use \Neomerx\Cors\Contracts\AnalyzerInterface;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;
use \Neomerx\CorsIlluminate\Adapters\IlluminateRequestToPsr7;
use \Illuminate\Contracts\Container\Container as ContainerInterface;

/**
 * @package Neomerx\CorsIlluminate
 */
class CorsMiddleware
{
    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param AnalyzerInterface  $analyzer
     * @param ContainerInterface $container
     */
    public function __construct(AnalyzerInterface $analyzer, ContainerInterface $container)
    {
        $this->analyzer  = $analyzer;
        $this->container = $container;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $cors = $this->getCorsAnalysis($request);

        switch ($cors->getRequestType()) {
            case AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
                $response = $next($request);
                break;

            case AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
                $response = new Response(null, Response::HTTP_OK, $cors->getResponseHeaders());
                break;

            case AnalysisResultInterface::TYPE_ACTUAL_REQUEST:
                /** @var Response $response */
                $response = $next($request);
                // merge CORS headers to response
                foreach ($cors->getResponseHeaders() as $name => $value) {
                    $response->headers->set($name, $value, false);
                }
                break;

            default:
                $response = $this->getResponseOnError($cors);
                break;
        }

        return $response;
    }

    /**
     * You can override this method in order to customize error reply.
     *
     * @param AnalysisResultInterface $analysisResult
     *
     * @return Response
     */
    protected function getResponseOnError(AnalysisResultInterface $analysisResult)
    {
        // avoid unused warning
        $analysisResult ?: null;

        return new Response(null, Response::HTTP_BAD_REQUEST);
    }

    /**
     * This method saves analysis result in Illuminate Container for
     * using it in other parts of the application (e.g. in exception handler).
     *
     * @param Request $request
     *
     * @return AnalysisResultInterface
     */
    protected function getCorsAnalysis(Request $request)
    {
        $analysis = $this->analyzer->analyze($this->getRequestAdapter($request));
        $this->container->instance(AnalysisResultInterface::class, $analysis);

        return $analysis;
    }

    /**
     * You can override this method to replace IlluminateRequestToPsr7 adapter with another one.
     *
     * @param Request $request
     *
     * @return RequestInterface
     */
    protected function getRequestAdapter(Request $request)
    {
        return new IlluminateRequestToPsr7($request);
    }
}
