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

use Native5\Core\Caching\Cache;

/**
 * DB
 */
class DB {
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

    protected $_config;
    protected $_conn;
    protected $_statementCache;

    /**
     * __construct Construct a DB object which wraps a PDO connection
     *
     * @param \Native5\Core\Database\DBConfig $config
     * @access public
     * @return void
     */
    public function __construct(\Native5\Core\Database\DBConfig $config) {
        // Store the config
        $this->_config = $config;
        // Connect to DB
        $this->_connect();
        // Setup statement cache
        $this->_statementCache = new Cache();
        $this->_statementCache->clear();
    }

    /**
     * __destruct Releases DB connection
     *
     * @access public
     * @return void
     */
    public function __destruct() {
        $this->_resetConnection();
    }

    /**
     * execQuery Wrapper method which prepares the query, binds values and executes it
     * 
     * @param string $query Query string
     * @param array $valArr array with statment placeholders mapped to values to bind
     * @param mixed $queryType Type of database query, one of SELECT, INSERT, UPDATE, DELETE constants
     *
     * @access public
     *
     * @return array|int|boolean SELECT - array of all selected rows as associative arrays on success, throws exception otherwise
     *                           INSERT- Database ID for inserted row, throws exception otherwise
     *                           UPDATE | DELETE- true on success, throws exception otherwise
     *
     * @throws Exception if could not prepare statement, or bind values or execute query successfuly
     */
    public function execQuery($query, $valArr = array(), $queryType = self::SELECT) {
        return $this->exec($this->bindValues($this->prepare($query), $valArr), $queryType);
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
            $this->checkConnection();
        return $this->_conn;
    }

    /**
     * beginTransaction Begin database transaction
     * 
     * @access public
     * @return void
     * @throws Exception if already inside a transaction of if could not begin transaction successfuly
     */
    public function beginTransaction() {
        // check if a transaction is already active
        if ($this->getConnection()->inTransaction())
            throw new \Exception("Already inside a DB transaction. You need to commit it before beginning a new one.");

        try {
            // No need to check for the connection again
            $this->getConnection(false)->beginTransaction();
        } catch (Exception $_e) {
            throw new \Exception("Error while beginning DB transaction: ".$pe->getMessage());
        }
    }

    /**
     * commitTransaction Commit begun database transaction
     * 
     * @access public
     * @return void
     * @throws Exception if not inside a transaction of if could not commit transaction successfuly
     */
    public function commitTransaction() {
        // check that a transaction is really active - do not check/renew the connection - renewing a connection breaks the transaction
        if (!$this->getConnection(false)->inTransaction())
            throw new \Exception("Not inside a DB transaction. Cannot commit.");

        try {
            $this->getConnection(false)->commit();
        } catch (Exception $_e) {
            throw new \Exception("Error while committing DB transaction: ".$pe->getMessage());
        }
    }

    /**
     * rollBackTransaction Rollback begun database transaction
     * 
     * @access public
     * @return void
     * @throws Exception if not inside a transaction of if could not rollback transaction successfuly
     */
    public function rollBackTransaction() {
        // check that a transaction is really active
        if (!$this->getConnection(false)->inTransaction())
            throw new \Exception("Not inside a DB transaction. Cannot commit.");

        try {
            $this->getConnection(false)->rollBack();
        } catch (Exception $_e) {
            throw new \Exception("Error while rolling back DB transaction: ".$pe->getMessage());
        }
    }

    // ****** Protected Functions Follow ****** //

    /**
     * prepare Prepare query from sql query
     * 
     * @param string $sql SQL query string
     *
     * @access protected
     * @return object prepared statement
     * @throws Exception if statement could not be prepared successfuly
     */
    protected function prepare($sql) {
        $this->checkConnection();

        $sqlKey = md5($sql);
        if ($this->_statementCache->exists($sqlKey))
            return $this->_statementCache->get($sqlKey);

        try {
            // No need to check the connection again
            $statement = $this->getConnection(false)->prepare($sql);
        } catch (\PDOException $pe) {
            throw new \Exception("Error in preparing statement:: query: ".$sql.PHP_EOL."Message: ".$pe->getMessage());
        }

        $this->_statementCache->set($sqlKey, $statement);
        return $statement;
    }

    /**
     * bindValues Bind values to a prepared statement
     * 
     * @param object $statement PDOStatement object
     * @param array $valArr array with statment placeholders mapped to values to bind
     *
     * @access protected
     * @return object prepared statement with bound values
     * @throws Exception if could not bind parameter to statement successfuly
     */
    protected function bindValues (\PDOStatement $statement, $valArr = array()) {
        if (empty($valArr))
            return $statement;

        /**
         * valArr can be of 2 types:
         *     type1: array( array(key1, value1, PDO::PARAM_INT), array(key2, value2 // if no 3rd param consider a string //), .. )
         *     type2: array( key1 => value1, key2 => value2, key3 => value3, .. ) or
         */
        $type1 = false;
        if (isset($valArr[0]) && is_array($valArr[0]) && isset($valArr[0][0]) && isset($valArr[0][1]))
            $type1 = true;

        foreach ($valArr as $idx=>$val) {
            try {
                if ($type1)
                    $statement->bindValue(
                        $val[0],
                        $val[1],
                        ((isset($val[2]) && $this->_checkIsPDOParamConstant($val[2])) ? $val[2] : \PDO::PARAM_STR)
                    );
                else
                    $statement->bindValue(
                        $idx,
                        $val,
                        \PDO::PARAM_STR
                    );
            } catch (\PDOException $pe) {
                throw new \Exception("Error in binding parameter:: query: ".$statement->queryString.PHP_EOL."Message: ".$pe->getMessage());
            }
        }

        return $statement;
    }

    private function _checkIsPDOParamConstant($param) {
        if (($param === \PDO::PARAM_BOOL) ||
            ($param === \PDO::PARAM_NULL) ||
            ($param === \PDO::PARAM_INT) ||
            ($param === \PDO::PARAM_STR) ||
            ($param === \PDO::PARAM_LOB)
        )
            return true;

        return false;
    }

    /**
     * exec Execute prepared statement on this database, reconnects to DB if connection does not exist
     * 
     * @param object $query PDO prepared statement to execute
     * @param mixed $type Type of database query - refer class constants
     * @param boolean $reconnect true if should reconnect to DB on a connection failure, false otherwise
     * @access protected
     * @return array|int|boolean SELECT - array of all selected rows as associative arrays on success, throws exception otherwise
     *                           INSERT- Database ID for inserted row, throws exception otherwise
     *                           UPDATE | DELETE- true on success, throws exception otherwise
     * @throws Exception if could not execute the query successfuly
     */
    protected function exec(\PDOStatement $statement, $type = self::SELECT) {
        // Execute
        try {
            $statement->execute();
        } catch (\PDOException $pe) {
            $statement->closeCursor();
            throw new \Exception("Error while executing query:: sql: ".$statement->queryString.PHP_EOL."Message: ".$pe->getMessage());
        }

        // Process Result based on the query type
        if ($type == self::SELECT) {
            $result = array();
            foreach($statement as $row) {
                $result[] = $row;
            }
        } else if ($type == self::INSERT) {
            // No need to check connection here
            $result = (int)$this->getConnection(false)->lastInsertId();
        } else {
            $result = true;
        }

        $statement->closeCursor();

        return $result;
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
    public function checkConnection() {
        try {
            $st = $this->_conn->query("SELECT 1");
        } catch(\PDOException $pe) {
            if ((strcasecmp($pe->getCode(), 'HY000') !== 0) && !stristr($pe->getMessage(), 'server has gone away'))
                throw $pe;

            if (function_exists('xdebug_start_trace'))
                xdebug_start_trace();

            $this->_resetConnection();
            $this->_connect();
        }

        return true;
    }

    /**
     * _resetConnection Release the database connection and clean statement cache
     * 
     * @access private
     * @return void
     */
    private function _resetConnection() {
        // Release the DB connection
        $this->_conn = null;
        unset($this->_conn);

        // Clear the prepared statement cache
        $this->_statementCache->clear();
    }
}

