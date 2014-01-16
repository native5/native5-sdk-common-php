<?php
/**
 *  Copyright 2012 Native5. All Rights Reserved
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *      You may not use this file except in compliance with the License.
 *
 *      Unless required by applicable law or agreed to in writing, software
 *      distributed under the License is distributed on an "AS IS" BASIS,
 *      WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *      See the License for the specific language governing permissions and
 *      limitations under the License.
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

use Native5\Core\Log\Impl\ILogHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\WebProcessor;
use Monolog\Formatter\LineFormatter;

/**
 * Reads & writes outputs to files using Monolog 
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
class AnalyticsLogHandler implements ILogHandler
{

    private $_logger;
    private $_name;

    // Syslog to Monolog mapping.
    private static $_mapping = array(
        'LOG_DEBUG'   => Logger::DEBUG,
        'LOG_INFO'    => Logger::INFO,
        'LOG_WARNING' => Logger::WARNING,
        'LOG_ERR'     => Logger::ERROR,
        'LOG_CRIT'    => Logger::CRITICAL,
        'LOG_ALERT'   => Logger::ALERT,
    );


    /**
     * Constructs a new file logger using given file path 
     * 
     * @param string $filePath Path to which to write log output to 
     * 
     * @access public
     * @return void
     */
    public function __construct($file='logs/analytics.log')
    {
        $this->logger = new Logger('route');
        $output = "%datetime% [%level_name%] %context% %message% \n";
        $formatter = new LineFormatter($output);
        $stream = new StreamHandler($file, Logger::INFO);
        $stream->setFormatter($formatter);
        $this->logger->pushHandler($stream);
    }//end __construct()


    /**
     * Writes log to file.
     * 
     * @param mixed $message  Message to write 
     * @param mixed $priority Priority of message to be written
     * 
     * @access public
     * @return void
     */
    public function writeLog($message, $priority='LOG_INFO',$context=array())
    {
        $this->logger->addRecord(
            self::$_mapping[$priority],
            $this->_transform($message),
            $context
        );

    }//end writeLog()


    /**
     * _transform 
     * 
     * @param mixed $message Message to transform before display
     *
     * @access private
     * @return void
     */
    private function _transform($message)
    {
        $msg = $message;
        return $msg;

    }//end _transform()


}//end class

