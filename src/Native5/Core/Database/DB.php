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

    private $_config;
    private $_conn;

    /**
     * __construct Construct a DB object which wraps a PDO connection
     *
     * @param \Native5\Core\Database\DBConfig $config
     * @access public
     * @return void
     */
    public function __construct(\Native5\Core\Database\DBConfig $config) {
        $this->_config = $config;
        $this->_connect();
    }

    /**
     * __destruct Releases DB connection
     *
     * @access public
     * @return void
     */
    public function __destruct() {
        unset($this->_conn);
        $this->_conn = null;
    }

    /**
     * getConnection Get the PDO Connection
     *
     * @param boolean $checkConnection If set to true the connection is checked for validity
     * @access public
     * @return mixed PDO Object representing database connection
     */
    public function getConnection($checkConnection = true) {
        if ($checkConnection)
            $this->_checkConnection();
        return $this->_conn;
    }

    /**
     * renew Renew the PDO Connection and return the renewed connection
     *
     * @access public
     * @return mixed PDO Object representing database connection
     */
    public function renew() {
        $this->_connect();
        return $this->_conn;
    }

    // ****** Private Functions Follow ****** //

    /**
     * _connect Make the PDO connection
     *
     * @access private
     * @return void
     */
    private function _connect()
    {
        if (empty($this->_config))
            throw new \Exception('Empty connection settings provided'); 
        
        $port = $this->_config->getPort();
        $port = !empty($port) ? $port : 3306;
        $dsn = $this->_config->getType().':host='.$this->_config->getHost().';port='.$port.';dbname='.$this->_config->getName();
        // Create a PDO Instance for this user + database combination
        try {
            $this->_conn = new \PDO($dsn, $this->_config->getUser(), $this->_config->getPassword());
        } catch(\PDOException $pe) {
            throw new \RuntimeException("Cannot connect to DB '".$this->_config->getName().
                "' with user '".$this->_config->getUser()."'".PHP_EOL."Message: ".$pe->getMessage());
        }

        $this->_conn->setAttribute(\PDO::ATTR_PERSISTENT, false);
        $this->_conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->_conn->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'UTF8'");
        $this->_conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->_conn->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
        $this->_conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
    }

    /**
     * _checkConnection  Checks if the PDO connection is active
     *
     * @access private
     * @return void
     */
    private function _checkConnection() {
        try {
            $st = $this->_conn->prepare("SELECT 1");
            $st->execute();
            $st->closeCursor();
        } catch(\PDOException $pe) {
            $st->closeCursor();
            if ((strcasecmp($pe->getCode(), 'HY000') !== 0) && !stristr($pe->getMessage(), 'server has gone away'))
                throw $pe;

            if (function_exists('xdebug_start_trace'))
                xdebug_start_trace();

            $this->_connect();
        }
    }
}

