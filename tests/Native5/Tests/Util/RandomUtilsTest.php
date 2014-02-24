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

use Native5\Util\RandomUtils;

/**
 * RandomUtilsTest 
 * 
 * @category  Core 
 * @package   Native5\Util
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0 
 * @link      http://www.docs.native5.com 
 * Created :  24-Feb-2014 
 * Last Modified : Mon Feb 24 13:42:04 2014
 */
class RandomUtilsTest  extends \PHPUnit_Framework_TestCase
{

    
    public function testRandomNumberGeneration()
    {
        $randUtils = new RandomUtils();
        $randomVals = array();
        for ($i = 0; $i < 10; $i++) {
            $randomVals[] = $randUtils->getBytes(10,true);
        }
        //assertNotNull);
        $randomVals = array_unique($randomVals);
        $this->assertCount(10, $randomVals);
    }
    
}

