<?php

use \Neomerx\CorsIlluminate\Settings\Settings;

return [

    /**
     * Could be string or array. If specified as array (recommended for better performance) it should
     * be in parse_url() result format.
     */
    Settings::KEY_SERVER_ORIGIN => [
        'scheme' => 'http',
        'host'   => 'localhost',
        'port'   => 8080,
    ],

    /**
     * A list of allowed request origins (lower-cased, no trail slashes).
     * Value `true` enables and value `null` disables origin.
     * If value is not on the list it is considered as not allowed.
     * Environment variables could be used for enabling/disabling certain hosts.
     */
    Settings::KEY_ALLOWED_ORIGINS => [
        'http://localhost'         => true,
        'http://some.disabled.com' => null,
    ],

    /**
     * A list of allowed request methods (case sensitive). Value `true` enables and value `null` disables method.
     * If value is not on the list it is considered as not allowed.
     * Environment variables could be used for enabling/disabling certain methods.
     *
     * Security Note: you have to remember CORS is not access control system and you should not expect all cross-origin
     * requests will have pre-flights. For so-called 'simple' methods with so-called 'simple' headers request
     * will be made without pre-flight. Thus you can not restrict such requests with CORS and should use other means.
     * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight request so
     * disabling it will not restrict access to resource(s).
     *
     * You can read more on 'simple' methods at http://www.w3.org/TR/cors/#simple-method
     */
    Settings::KEY_ALLOWED_METHODS => [
        'GET'    => true,
        'PATCH'  => true,
        'POST'   => true,
        'PUT'    => true,
        'DELETE' => true,
    ],

    /**
     * A list of allowed request headers (lower-cased). Value `true` enables and value `null` disables header.
     * If value is not on the list it is considered as not allowed.
     * Environment variables could be used for enabling/disabling certain headers.
     *
     * Security Note: you have to remember CORS is not access control system and you should not expect all cross-origin
     * requests will have pre-flights. For so-called 'simple' methods with so-called 'simple' headers request
     * will be made without pre-flight. Thus you can not restrict such requests with CORS and should use other means.
     * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight request so
     * disabling it will not restrict access to resource(s).
     *
     * You can read more on 'simple' headers at http://www.w3.org/TR/cors/#simple-header
     */
    Settings::KEY_ALLOWED_HEADERS => [
        'content-type'            => null,
        'x-custom-request-header' => null,
    ],

    /**
     * A list of headers (case insensitive) which will be made accessible to user agent (browser) in response.
     * Value `true` enables and value `null` disables header.
     * If value is not on the list it is considered as not allowed.
     * Environment variables could be used for enabling/disabling certain headers.
     *
     * For example,
     *
     * public static $exposedHeaders = [
     *     'content-type'             => true,
     *     'x-custom-response-header' => null,
     * ];
     */
    Settings::KEY_EXPOSED_HEADERS => [
        'content-type'             => null,
        'x-custom-response-header' => null,
    ],

    /**
     * If access with credentials is supported by the resource.
     */
    Settings::KEY_IS_USING_CREDENTIALS => false,

    /**
     * Pre-flight response cache max period in seconds.
     */
    Settings::KEY_PRE_FLIGHT_MAX_AGE => 0,

];
