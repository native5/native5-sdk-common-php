<?php
/**
 * Copyright © 2013 Native5
 * 
 * All Rights Reserved.  
 * Licensed under the Native5 License, Version 1.0 (the "License"); 
 * You may not use this file except in compliance with the License. 
 * You may obtain a copy of the License at
 *  
 *      http://www.native5.com/legal/npl-v1.html
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  PHP version 5.3+
 */

namespace Native5\Util;

use Guzzle\Http\Client;

/**
 * UrlShortener 
 * 
 * @category  Core 
 * @package   Native5\Commons\Utils
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0 
 * @link      http://www.docs.native5.com 
 * Created :  22-Nov-2013 
 * Last Modified : Fri Nov 22 13:54:43 2013
 */
class UrlShortener
{

    private static $_ACCESS_KEY = "e51921c77366757f2ead27ef50eec8694a08dc82"; 
    
    private static $_BITLY_URL = "https://api-ssl.bitly.com/v3/shorten";
    
    
    /**
     * Shortens a URL to a bit.ly url 
     * 
     * @param mixed $url 
     * @static
     * @access public
     * @return void
     */
    public static function shorten($url)
    {
        $client = new Client();
        $request = $client->get(self::$_BITLY_URL);
        $request->getQuery()->add('access_token', self::$_ACCESS_KEY);
        $request->getQuery()->add('longUrl', $url);
        $resp = $request->send();
        $jsonRep = $resp->json();
        return $jsonRep['data']['url'];
    }
    
}

