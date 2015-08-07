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
use \Neomerx\Cors\Analyzer;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use \Neomerx\Cors\Contracts\AnalyzerInterface;
use \Neomerx\CorsIlluminate\Settings\Settings;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;
use \Neomerx\CorsIlluminate\Adapters\IlluminateRequestToPsr7;

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
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(AnalyzerInterface $analyzer = null)
    {
        $this->analyzer = $analyzer !== null ? $analyzer : Analyzer::instance(new Settings());
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
        $cors = $this->analyzer->analyze(new IlluminateRequestToPsr7($request));

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
}
