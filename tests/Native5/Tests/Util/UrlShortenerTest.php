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

namespace Native5\Tests\Util;

use Native5\Util\UrlShortener;

/**
 * UrlShortenerTest 
 * 
 * @category  Core 
 * @package   Native5\Tests\Util
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0 
 * @link      http://www.docs.native5.com 
 * Created :  22-Nov-2013 
 * Last Modified : Fri Nov 22 14:35:05 2013
 */
class UrlShortenerTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * testUrlShortener 
     * 
     * @access public
     * @return void
     */
    public function testUrlShortener()
    {
        $appsUrl = "http://apps.dev.native5.com/zEJ9gNYsL1383888248/";
        $shortUrl = UrlShortener::shorten($appsUrl);
        $this->assertFalse(empty($shortUrl));
    }
}

