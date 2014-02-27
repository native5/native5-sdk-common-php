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
 */

namespace Native5\Core\Configuration;
use Symfony\Component\Yaml\Yaml;

/**
 * YamlConfigFactory
 *
 * @category  Configuration
 * @package   Native5\Core\Configuration
 * @author    Shamik Datta <shamik@native5.com>
 * @copyright 2012 Native5. All Rights Reserved
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0
 * @link      http://www.docs.native5.com
 */
class YamlConfigFactory extends \Native5\Core\Configuration\ArrayConfigFactory {

    /**
     * __construct
     *
     * @param mixed $configFile Path to base yaml configuration file
     * @param mixed $overridingConfigFile Path to overriding yaml configuration file
     *
     * @access public
     * @return void
     */
    public function __construct($configFile = null, $overridingConfigFile = null) {
        if (!empty($configFile))
            parent::__construct($this->_parse($configFile));
        $this->override($overridingConfigFile);
    }

    /**
     * makeConfig Futher process the configuration, eg., wrap the associative array inside a getter/setter class
     *
     * @abstract
     * @access protected
     * @return void
     */
    protected function makeConfig() {
        return $this->_config;
    }

    /**
     * override Merges the configuration from this yaml file with the base configuration, override values
     *
     * @param mixed $config path to config file
     * @param mixed $strict whether to throw an exception if file is not found
     *
     * @access public
     * @return void
     */
    public function override($configFile, $strict = false) {
        parent::override($this->_parse($configFile, $strict));
    }

    // ****** Private Functions Follow ****** //

    private function _parse($configFile, $exception = true) {
        if ((empty($configFile) || !file_exists($configFile))) {
            if ($exception)
                throw new \Exception("Empty yaml config file or file does not exist: ".$configFile);
            else
                return array();
        }

        $configArr = array();
        $yaml = new \Symfony\Component\Yaml\Parser();

        try {
            $configArr = $yaml->parse(file_get_contents($configFile));
        } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
            $GLOBALS['logger']->info("Unable to parse the file [ %s ]: %s", $configFile, $e->getMessage());
            if ($exception)
                throw new \Exception("Not a valid yaml file: ".$configFile);
        }

        return $configArr;
    }
}

