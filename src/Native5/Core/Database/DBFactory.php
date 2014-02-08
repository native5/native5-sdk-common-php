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
 * @category  Database
 * @package   Native5\Core\Connectors\Database
 * @author    Shamik Datta <shamik@native5.com>
 * @copyright 2012 Native5. All Rights Reserved
 * @license   See attached LICENSE for details
 * @version   GIT: $gitid$
 * @link      http://www.docs.native5.com
 */

namespace Native5\Core\Database;

class DBFactory
{
    private static $_dbs;

    /**
     * instance method for instantiating a DB object
     * 
     * @param mixed $configuration Database configuration
     *
     * @static
     * @access private
     * @return void
     */
    public static function makeDB(\Native5\Core\Database\DBConfig $configuration)
    {
        if (empty($configuration))
            throw new \Exception('Empty connection settings provided'); 

        $port = $configuration->getPort();
        $port = !empty($port) ? $port : 3306;
        $dsn = $configuration->getType().':host='.$configuration->getHost().';port='.$port.';dbname='.$configuration->getName();
        $dbKey = md5($dsn.'.'.$configuration->getUser());

        if (isset(self::$_dbs[$dbKey]) && !empty(self::$_dbs[$dbKey]) && self::$_dbs[$dbKey]->checkConnection())
            return self::$_dbs[$dbKey];

        // Create a DB Instance for this user + database combination
        return (self::$_dbs[$dbKey] = new \Native5\Core\Database\DB($configuration));
    }

    private function __construct() {}
}

