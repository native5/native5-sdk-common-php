<?php

/**
 * AbstractConnector 
 * 
 * @abstract
 * @package 
 * @version GIT: $gitid$
 * @copyright 2012 Native5. All Rights Reserved
 * @author Barada Sahu <barry@native5.com> 
 * @license See attached LICENSE for details. Native5 License V1.0 {@link http://www.native5.com/license/1_0.txt}
 */
abstract class AbstractConnector {


    abstract function connect($key, $params);


    abstract function beginTransaction(); // optional method for systems supporting transactional paradigms.


    abstract function call($op="default", $params, $tx=null);


    abstract function sync();


    abstract function endTransaction(); // optional method for systems supporting transactional paradigms.


}//end class

?>
