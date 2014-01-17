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
 * ArrayConfigFactory
 *
 * @category  Configuration
 * @package   Native5\Core\Configuration
 * @author    Shamik Datta <shamik@native5.com>
 * @copyright 2012 Native5. All Rights Reserved
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0
 * @link      http://www.docs.native5.com
 */
abstract class ArrayConfigFactory
{
    protected $_config = array();

    /**
     * __construct
     *
     * @param mixed $config The base configuration associative array
     *
     * @access public
     * @return void
     */
    public function __construct($config = null) {
        self::_checkArray($config);
        $this->_config = $config;
    }

    /**
     * makeConfig Futher process the configuration, eg., wrap the associative array inside a getter/setter class
     *
     * @abstract
     * @access protected
     * @return void
     */
    abstract protected function makeConfig();

    /**
     * override Merge with configuration array, this array overrides the current values
     * override Merges this configuration array with the base configuration, override values
     *
     * @param mixed $config Overriding configuration array
     * @access public
     * @return void
     * @note needs to be called after setMasterConfig()
     */
    public function override($config) {
        self::_checkArray($config);
        $this->_config = array_replace_recursive($this->_config, $config);
    }

    /**
     * getConfig get the merged configuration wrapped inside a Configuration class
     *
     * @access public
     * @return void
     * @note should be called only after you have set your master and local configs
     */
    public function getConfig() {
        return $this->makeConfig();
    }

    /**
     * getRawConfig get the merged configuration as an associative array
     *
     * @access public
     * @return void
     * @note should be called only after you have set your master and local configs
     */
    public function getRawConfig() {
        return $this->_config;
    }

    /**
     * setRawConfig force to use this configuration instead of the one used during construction and overriding
     *
     * @param mixed $config
     * @access public
     * @return void
     */
    public function setRawConfig($config) {
        self::_checkArray($config);
        $this->_config = $config;
    }

    // ****** Private Functions Follow ****** //

    private static function _checkArray($config) {
        if (!empty($config) && !is_array($config))
            throw new \InvalidArgumentException("Config must be an array");
    }
}

