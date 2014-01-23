<?php
/**
 *  Copyright 2012 Native5. All Rights Reserved
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *	You may not use this file except in compliance with the License.
 *
 *	Unless required by applicable law or agreed to in writing, software
 *	distributed under the License is distributed on an "AS IS" BASIS,
 *	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *	See the License for the specific language governing permissions and
 *	limitations under the License.
 *  PHP version 5.3+
 */

namespace Native5\Core\Http;

/**
 * @category  Http
 * @package   Native5\Core\Http
 * @author    Shamik <shamik@native5.com>
 * @copyright 2013 Native5. All Rights Reserved
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0
 * @link      http://www.docs.native5.com
 * Created :  25-10-2013
 * Last Modified : Fri Oct 25 09:11:53 2013
 */
class StatusCodes {
    // 1xx Provisional Responses
    const CONTINUE_SENDING = 100;
    const SWITCHING_PROTOCOLS = 101;
    const PROCESSING = 102;

    // 2xx Success Responses
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NONAUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    const MULTI_STATUS = 207;
    const ALREADY_REPORTED = 208;

    // 3xx redirection responses
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const MOVED_TEMPORARILY = 302;
    const SEE_ANOTHER_RESPONSE = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const SWITCH_PROXY = 306;
    const REDIRECTED_TEMPORARILY = 307;
    const REDIRECTED_PERMANENTLY = 308;

    // 4xx Errors
    const BAD_REQUEST = 400;
    const NOT_AUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const REQUEST_URI_TOO_LONG = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUEST_RANGE_NOT_SATISFIABLE = 416;
    const EXCEPTION_FAILED = 417;

    // 5xx Errors
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_UNSUPPORTED = 505;
}

