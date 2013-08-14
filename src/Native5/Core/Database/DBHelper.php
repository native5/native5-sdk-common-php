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
 * @category  Connectors
 * @package   Native5\Core\Connectors\Database
 * @author    Shamik Datta <shamik@native5.com>
 * @copyright 2012 Native5. All Rights Reserved
 * @license   See attached LICENSE for details
 * @version   GIT: $gitid$
 * @link      http://www.docs.native5.com
 */

namespace Native5\Core\Database;

/**
 * DBHelper
 */
class DBHelper {
    /**
     * Constant indicating execution of a SELECT query 
     *
     * @access public
     */
    const SELECT = 0;
    /**
     * Constant indicating execution of an INSERT query 
     *
     * @access public
     */
    const INSERT = 1;
    /**
     * Constant indicating execution of an UPDATE query 
     *
     * @access public
     */
    const UPDATE = 2;
    /**
     * Constant indicating execution of a DELETE query 
     *
     * @access public
     */
    const DELETE = 3;

    private $_config;
    private $_con;

    /**
     * __construct  Create a DB object
     * 
     * @param mixed[] $configuration Database configuration {
     *     @type string "type" Database type as used in PDO DSN
     *     @type string "host" Database host
     *     @type integer "name" Database name
     *     @type boolean "username" Database username
     *     @type string "password" Database password
     * }
     * 
     * @throws InvalidArgumentException if configuration is not well formed
     * @throws RuntimeException if cannot connect to DB passed in configuration
     *
     * @access public
     * @return void
     */
    public function __construct($configuration) {
        $this->_config = $configuration;
        $this->dbConInit();
    }

    /**
     * __destruct Releases DB connection
     * 
     * @access public
     * @return void
     */
    public function __destruct() {
        $this->_con = null;
    }

    /**
     * prepare Prepare query from sql query
     * 
     * @param string $sql SQL query string
     * @access public
     * @return object prepared statement
     * @throws PDOException if query was not successful
     */
    public function prepare ($sql) {
        return $this->_con->prepare($sql);
    }

    /**
     * exec Execute prepared statement on this database, reconnects to DB if connection does not exist
     * 
     * @param object $query PDO prepared statement to execute
     * @param mixed $type Type of database query - refer class constants
     * @param boolean $reconnect true if should reconnect to DB on a connection failure, false otherwise
     * @access public
     * @return array|int|boolean SELECT - array of all selected rows as associative arrays on success, throws exception otherwise
     *                           INSERT- Database ID for inserted row, throws exception otherwise
     *                           UPDATE | DELETE- true on success, throws exception otherwise
     * @throws PDOException if cannot execute the query
     */
    public function exec ($statement, $type = self::SELECT) {
        // Execute
        try {
            $statement->execute();
        } catch (\PDOException $pe) {
            $statement->closeCursor();
            $this->dbConInit(); // try reconnecting once
            $statement->execute();
        }

        // Process Result based on the query type
        if ($type == self::SELECT) {
            $result = array();
            foreach($statement as $row) {
                $result[] = $row;
            }
        } else if ($type == self::INSERT) {
            $result = (int)$this->_con->lastInsertId();
        } else {
            $result = true;
        }

        $statement->closeCursor();

        return $result;
    }

    /**
     * dbConInit Initializes the PDO database object for the provided database and user
     * 
     * @access private
     * @return boolean true on success, false otherwise
     */
    private function dbConInit () {
        // Check the configuration
        if (empty($this->_config) || !is_array($this->_config))
            throw new \InvalidArgumentException("Configuration should be an array");
        else if (empty($this->_config['type']))
            throw new \InvalidArgumentException("DB type not specified in configuration");
        else if (empty($this->_config['host']))
            throw new \InvalidArgumentException("DB host not specified in configuration");
        else if (empty($this->_config['name']))
            throw new \InvalidArgumentException("DB name not specified in configuration");
        else if (empty($this->_config['username']))
            throw new \InvalidArgumentException("DB username not specified in configuration");
        else if (empty($this->_config['password']))
            throw new \InvalidArgumentException("DB password not specified in configuration");

        $dsn = $this->_config['type'].":host=".$this->_config['host'].";dbname=".$this->_config['name'];

        $opt = array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // throw exceptions when error occur
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", // set UTF8 names
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // always fetch only associative arrays
            \PDO::ATTR_PERSISTENT => true, // use persistent connections
            \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_TO_STRING // convert NULLs to empty strings
        );

        try {
            $this->_con = new \PDO($dsn, $this->_config['username'], $this->_config['password'], $opt);
        } catch(\PDOException $pe) {
            throw new \RuntimeException("Cannot connect to DB '".$this->_config['name']."' with user '".$this->_config['username']."'".PHP_EOL.
                    "Message: ".$pe->getMessage());
        }

        return true;
    }

}//end class

