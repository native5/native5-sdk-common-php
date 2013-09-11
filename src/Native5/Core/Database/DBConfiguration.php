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

class DBConfiguration {
    private $_config;

    /**
     * setMasterConfig sets the master (base) configuration file
     * 
     * @param mixed $config path to master config file
     * @access public
     * @return void
     * @note needs to be called before setLocalConfig()
     */
    public function setMasterConfig($config) {
        $this->_config = $this->_parse($config);
    }

    /**
     * setLocalConfig sets the local (override) configuration file
     * 
     * @param mixed $config path to local config file
     * @param mixed $strict whether to throw an exception if file is not found
     * @access public
     * @return void
     * @note needs to be called after setMasterConfig()
     */
    public function setLocalConfig($config, $strict = false) {
        $localConfig = $this->_parse($config, $strict);
        
        if (!empty($localConfig))
            $this->_config = array_replace_recursive($this->_config, $localConfig);
    }

    /**
     * getConfiguration get the merged configuration
     * 
     * @access public
     * @return void
     * @note should be called only after you have set your master and local configs
     */
    public function getConfiguration() {
        return $this->_config;
    }

    private function _parse($config, $exception = true) {
        $configArr = array();

        if ((empty($config) || !file_exists($config)) && $exception)
            throw new \Exception("Empty config file or file does not exist: ".$config);

        if (!($configArr = @yaml_parse_file($config)) && $exception)
            throw new \Exception("Not a valid yaml file: ".$config);

        return $configArr;
    }
}

