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
use \Psr\Http\Message\RequestInterface;
use \Neomerx\Cors\Contracts\AnalyzerInterface;
use \Neomerx\CorsIlluminate\Settings\Settings;
use \Neomerx\Cors\Contracts\AnalysisResultInterface;
use \Neomerx\Cors\Contracts\AnalysisStrategyInterface;
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
     * @param AnalyzerInterface|null $analyzer
     */
    public function __construct(AnalyzerInterface $analyzer = null)
    {
        $this->analyzer = $analyzer;
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
     * @return AnalyzerInterface
     */
    protected function getAnalyzer()
    {
        if ($this->analyzer === null) {
            $this->analyzer = Analyzer::instance($this->getSettings());
        }

        return $this->analyzer;
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
     * You can override this method to save its return result (e.g. in Illuminate Container) for
     * using it in other parts of the application (e.g. in exception handler).
     *
     * @param Request $request
     *
     * @return AnalysisResultInterface
     */
    protected function getCorsAnalysis(Request $request)
    {
        return $this->getAnalyzer()->analyze($this->getRequestAdapter($request));
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

    /**
     * You can override this class if more customized `AnalysisStrategyInterface` behaviour is needed.
     *
     * @return AnalysisStrategyInterface
     */
    protected function getSettings()
    {
        return new Settings();
    }
}
