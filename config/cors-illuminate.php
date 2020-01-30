<?php declare(strict_types = 1);

use \Neomerx\CorsIlluminate\Settings\Settings as S;

return [

    /**
     * If CORS handling should be logged. Debugging feature.
     */
    S::KEY_LOGS_ENABLED  => false,

    /**
     * Could be string or array. If specified as array (recommended for better performance) it should
     * be in parse_url() result format.
     */
    S::KEY_SERVER_ORIGIN => [
        'scheme' => 'http',
        'host'   => 'localhost',
        'port'   => 8080,
    ],

    /**
     * A list of allowed request origins (no trail slashes).
     * If value is not on the list it is considered as not allowed.
     * If you want to allow all origins remove/comment this section.
     */
    S::KEY_ALLOWED_ORIGINS => [
        'http://localhost',
    ],

    /**
     * A list of allowed request methods. * If value is not on the list it is considered as not allowed.
     *
     * Security Note: you have to remember CORS is not access control system and you should not expect all cross-origin
     * requests will have pre-flights. For so-called 'simple' methods with so-called 'simple' headers request
     * will be made without pre-flight. Thus you can not restrict such requests with CORS and should use other means.
     * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight request so
     * disabling it will not restrict access to resource(s).
     *
     * You can read more on 'simple' methods at http://www.w3.org/TR/cors/#simple-method
     */
    S::KEY_ALLOWED_METHODS => [
        'GET',
        'PATCH',
        'POST',
        'PUT',
        'DELETE',
    ],

    /**
     * A list of allowed request headers. If value is not on the list it is considered as not allowed.
     *
     * Security Note: you have to remember CORS is not access control system and you should not expect all cross-origin
     * requests will have pre-flights. For so-called 'simple' methods with so-called 'simple' headers request
     * will be made without pre-flight. Thus you can not restrict such requests with CORS and should use other means.
     * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight request so
     * disabling it will not restrict access to resource(s).
     *
     * You can read more on 'simple' headers at http://www.w3.org/TR/cors/#simple-header
     */
    S::KEY_ALLOWED_HEADERS => [
        'Content-Type',
    ],

    /**
     * A list of headers (case insensitive) which will be made accessible to user agent (browser) in response.
     */
    S::KEY_EXPOSED_HEADERS => [
        'Content-Type',
    ],

    /**
     * If access with credentials is supported by the resource.
     */
    S::KEY_IS_USING_CREDENTIALS => false,

    /**
     * Pre-flight response cache max period in seconds.
     */
    S::KEY_FLIGHT_CACHE_MAX_AGE => 0,

    /**
     * If allowed methods should be added to pre-flight response when 'simple' method is requested.
     */
    S::KEY_IS_FORCE_ADD_METHODS => false,

    /**
     * If allowed headers should be added when request headers are 'simple' and
     * non of them is 'Content-Type'.
     */
    S::KEY_IS_FORCE_ADD_HEADERS => false,

    /**
     * If request 'Host' header should be checked against server's origin.
     */
    S::KEY_IS_CHECK_HOST => false,

];
