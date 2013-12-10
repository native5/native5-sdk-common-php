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
 *
 * @category  Log 
 * @package   Native5\Core\Log\Impl 
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached LICENSE for details
 * @version   GIT: $gitid$ 
 * @link      http://www.docs.native5.com 
 */

namespace Native5\Core\Log\Impl;

use Native5\Core\Log\Impl\AbstractLogAdapter;

/**
 * Concrete Implementation of Logger using Monolog as a backend 
 * 
 * @category  Log 
 * @package   Native5\Core\Log\Impl 
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0 
 * @link      http://www.docs.native5.com 
 * Created : 27-11-2012
 * Last Modified : Fri Dec 21 09:11:53 2012
 */
class MonologAdapter extends AbstractLogAdapter
{
    public function getHandler($destination, $level='LOG_INFO', $type='file')
    {
        switch ($type) {
        case 'file':
            return new FileLogHandler($destination, $level);
        default:
            return new SysLogHandler($level);
        }
    }
}
