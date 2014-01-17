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

/**
 * DBDAO Base DAO class for creating Mysql Database based DAOs
 */
class DBDAO {
    protected $db;
    protected $queries;

    /**
     * __construct Private constructor which creates the DB object
     *
     * @param \Native5\Core\Database\DBConfig $config Instance of DBConfig class containing the database connection parameters
     *
     * @access private
     * @return mixed Instance of DBDAO (this) class
     */
    private function __construct(\Native5\Core\Database\DB $db) {
        $this->db = $db;
    }

    /**
     * makeFromDB Creates the DB connection using an instance of the DB class
     *
     * @access protected
     * @return mixed Instance of DBDAO (this) class
     */
    protected static function makeFromDB (\Native5\Core\Database\DB $db) {
        if (empty($db))
            throw new \InvalidArgumentException("Emoty DB object received");

        return new self($db);
    }

    /**
     * makeFromSettings Creates the DB connection using settings in database section of settings.yml file
     *
     * @access protected
     * @return mixed Instance of DBDAO (this) class
     */
    protected static function makeFromSettings () {
        try {
            // Get the database configuration from settings.yml file
            $instance = new self(
                \Native5\Core\Database\DBFactory::makeDB(
                    $this->_makeDBConfigFromArray(
                        $GLOBALS['app']->getConfiguration()->getRawConfiguration('database')
                    )
                )
            );
        } catch (\InvalidArgumentException $_e) {
            throw new \InvalidArgumentException("Empty or invalid database section in settings.yml configuration file");
        }

        return $instance;
    }

    /**
     * makeFromConfigurationFiles Creates the DB connection using settings in database section in yaml configuration file(s)
     *
     * @param mixed $configFile Base yaml database configuration file
     * @param mixed $overrideConfigFile Overriding yaml database configuration file
     *
     * @access protected
     * @return mixed Instance of DBDAO (this) class
     */
    protected static function makeFromConfigurationFiles ($configFile, $overrideConfigFile = null) {
        $dbConfigFactory = new \Native5\Core\Database\DBConfigFactory($configFile);
        if (!empty($overrideConfigFile))
            $dbConfigFactory->override($overrideConfigFile);

        return new self(
            \Native5\Core\Database\DBFactory::makeDB($dbConfigFactory->getConfig())
        );
    }

    /**
     * makeFromConfigurationArray Creates the DB connection using parameters in assoc. array
     * 
     * @param mixed[] $dbConfigArray DB configuration array in the following format
     *  mixed[] $dbConfigArray {
     *     @type string "type" Database type as used in PDO DSN
     *     @type string "host" Database host
     *     @type string "port" Database port
     *     @type string "name" Database name
     *     @type string "username" Database username
     *     @type string "password" Database password
     * }
     *
     * @access protected
     * @return mixed Instance of DBDAO (this) class
     */
    protected function makeFromConfigurationArray($dbConfigArray) {
        try {
            // Use the database configuration from the provided $dbConfigArray
            $instance = new self(
                \Native5\Core\Database\DBFactory::makeDB(
                    $this->_makeDBConfigFromArray($dbConfigArray)
                )
            );
        } catch (\InvalidArgumentException $_e) {
            throw new \InvalidArgumentException("Empty or invalid database configuration array provided");
        }

        return $instance;
    }

    /**
     * setYamlQueries Set the name of the yaml file with sql queries indexed by query names
     * 
     * @param mixed $sqlQueriesFile Filesystem path to yaml file
     *
     * @access protected
     * @return void
     */
    protected function loadQueries($sqlQueriesFile) {
        // Read the sql queries file
        if (!file_exists($sqlQueriesFile))
            throw new \Exception("File with mysql queries not found at expected location: $sqlQueriesFile");

        if (!($this->queries = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($sqlQueriesFile))))
            throw new \Exception("Could not parse yaml file with mysql queries: $sqlQueriesFile");
    }

    /**
     * execQuery Execute Indexed Query
     * 
     * @param mixed $queryIndex Index of the query to be picked from the sql queries file
     * @param mixed $valArr Key Value pairs to bind before query is executed
     * @param mixed $type One of DB::SELECT, DB::INSERT, DB::UPDATE, DB::DELETE
     * @access protected
     * @return void
     */
    protected function execQuery ($queryIndex, $valArr, $type = \Native5\Core\Database\DB::SELECT) {
        return $this->db->execQuery($this->queries[$queryIndex], $valArr, $type);
    }

    /**
     * execQueryString Execute Query String
     * 
     * @param mixed $query Query string
     * @param mixed $valArr Key Value pairs to bind before query is executed
     * @param mixed $type One of DB::SELECT, DB::INSERT, DB::UPDATE, DB::DELETE
     * @access protected
     * @return void
     */
    protected function execQueryString ($query, $valArr, $type = \Native5\Core\Database\DB::SELECT) {
        return $this->db->execQuery($query, $valArr, $type);
    }

    // ****** Private Function Follow ****** //

    private function _makeDBConfigFromArray($dbConfigArray) {
        if (empty($dbConfigArray) || !is_array($dbConfigArray))
            throw new \InvalidArgumentException();

        // Create the database configuration
        $dbConfigFactory = new \Native5\Core\Database\DBConfigFactory();
        $dbConfigFactory->setRawConfig($dbConfigArray);

        return $dbConfigFactory->getConfig();
    }
}

