<?php
/**
 *  Copyright 2012 Native5. All Rights Reserved
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  You may not use this file except in compliance with the License.
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *  PHP version 5.3+
 *
 * @category  Sessions
 * @package   Native5\Core\Connectors\Database
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved
 * @license   See attached LICENSE for details
 * @version   GIT: $gitid$
 * @link      http://www.docs.native5.com
 */

namespace Native5\Core\Database;

/**
 * DB 
 *
 * @category  Connectors 
 * @package   Native5\Core\Connectors\Database
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0
 * @link      http://www.docs.native5.com
 * Created : 27-11-2012
 * Last Modified : Fri Dec 21 09:11:53 2012
 */
class DB
{

    private static $_db;


    /**
     * instance 
     * 
     * @param mixed $configuration Database configuration
     *
     * @static
     * @access public
     * @return void
     */
    public static function instance($configuration=null)
    {
        if (empty(self::$_db) === true) {
            if (!empty($configuration)) {
                $dsn = 'mysql:host='.$configuration['host'].';dbname='.$configuration['name'];
                self::$_db = new \PDO($dsn, $configuration['user'], $configuration['password']);
            } else {
                self::$_db = new \PDO('mysql:host='.DBConfig::HOST.';dbname='.DBConfig::NAME, DBConfig::USER, DBConfig::PASSWD);
            }

            self::$_db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            self::$_db->setAttribute(\PDO::ATTR_PERSISTENT, true);
            self::$_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$_db;

    }//end instance()


    /**
     * Factory method for instantiating a db object. 
     * 
     * @static
     * @access public
     * @return void
     */
    public static function factory()
    {
        return new self;

    }//end factory()


    private function __construct() {}


}//end class

?>
