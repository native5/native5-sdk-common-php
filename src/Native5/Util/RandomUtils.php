<?php
/**
 * Copyright © 2013 Native5
 * 
 * All Rights Reserved.  
 * Licensed under the Native5 License, Version 1.0 (the "License"); 
 * You may not use this file except in compliance with the License. 
 * You may obtain a copy of the License at
 *  
 *      http://www.native5.com/legal/npl-v1.html
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  PHP version 5.3+
 */

namespace Native5\Util;

/**
 * RandomUtils 
 * 
 * Adapted from https://github.com/padraic/SecurityMultiTool. 
 *
 * @category  Core 
 * @package   Native5\Util
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0 
 * @link      http://www.docs.native5.com 
 * Created :  24-Feb-2014 
 * Last Modified : Mon Feb 24 11:22:55 2014
 */

use RandomLib;

class RandomUtils 
{

    protected $generator = null;


    /**
     * PRNG generator based on security principles 
     * at http://phpsecurity.readthedocs.org/en/latest/Insufficient-Entropy-For-Random-Values.html 
     * 
     * @param mixed $length 
     * @param mixed $strong 
     * @access public
     * @return void
     */
    public function getBytes($length, $strong = false)
    {
        $bytes = '';
        if (function_exists('openssl_random_pseudo_bytes')
            && (version_compare(PHP_VERSION, '5.3.4') >= 0
            || strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        ) {
            $bytes = openssl_random_pseudo_bytes($length, $usable);
            if (true === $usable) {
                return $bytes;
            }
        }
        if (function_exists('mcrypt_create_iv')
            && (version_compare(PHP_VERSION, '5.3.7') >= 0
            || strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        ) {
            $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            if ($bytes !== false && strlen($bytes) === $length) {
                return $bytes;
            }
        }
        $checkAlternatives = (file_exists('/dev/urandom') && is_readable('/dev/urandom'))
            || class_exists('\\COM', false);
        if (true === $strong && false === $checkAlternatives) {
            throw new \Exception(
                'Unable to generate sufficiently strong random bytes due to a lack ',
                'of sources with sufficient entropy'
            );
        }
        $generator = $this->getAlternativeGenerator();
        return $generator->generate($length);
    }

    public function getAlternativeGenerator()
    {
        if (isset($this->generator)) {
            return $this->generator;
        }
        $factory = new RandomLib\Factory;
        $this->generator = $factory->getMediumStrengthGenerator();
        return $this->generator;
    }

    public function getBoolean($strong = false)
    {
        $byte = $this->getBytes(1, $strong);
        return (bool) (ord($byte) % 2);
    }

    public function getInteger($min, $max, $strong = false)
    {
        if ($min > $max) {
            throw new \RuntimeException(
                'The min parameter must be lower than max parameter'
            );
        }
        $range = $max - $min;
        if ($range == 0) {
            return $max;
        } elseif ($range > PHP_INT_MAX || is_float($range)) {
            throw new \RangeException(
                'The supplied range between min and max must result in an integer '
                . 'no greater than the value of PHP_INT_MAX'
            );
        }
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(self::getBytes($bytes, $strong)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);
        return ($min + $rnd);
    }

    public function getFloat($strong = false)
    {
        $bytes = static::getBytes(7, $strong);
        $bytes[6] = $bytes[6] | chr(0xF0);
        $bytes .= chr(63); // exponent bias (1023)
        list(, $float) = unpack('d', $bytes);
        return ($float - 1);
    }

    public function getString($length, $charlist = '', $strong = false)
    {
        if ($length < 1) {
            throw new \Exception(
                'String length must be greater than zero'
            );
        }
        if (empty($charlist)) {
            $numBytes = ceil($length * 0.75);
            $bytes = $this->getBytes($numBytes, $strong);
            return substr(rtrim(base64_encode($bytes), '='), 0, $length);
        }
        $listLen = strlen($charlist);
        if ($listLen == 1) {
            return str_repeat($charlist, $length);
        }
        $bytes = $this->getBytes($length, $strong);
        $pos = 0;
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $pos = ($pos + ord($bytes[$i])) % $listLen;
            $result .= $charlist[$pos];
        }
        return $result;
    }

}
