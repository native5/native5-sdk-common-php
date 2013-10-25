<?php
/**
 *  Copyright 2012 Native5. All Rights Reserved
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *	You may not use this file except in compliance with the License.
 *
 *	Unless required by applicable law or agreed to in writing, software
 *	distributed under the License is distributed on an "AS IS" BASIS,
 *	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *	See the License for the specific language governing permissions and
 *	limitations under the License.
 *  PHP version 5.3+
 *
 * @category  API 
 * @package   Native5\Core\<package>
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached LICENSE for details
 * @version   GIT: $gitid$ 
 * @link      http://www.docs.native5.com 
 */

namespace Native5\Services\Common;

use Guzzle\Http\Client;

/**
 * ApiClient 
 * 
 * @category  API 
 * @package   Native5\Core\Api
 * @author    Barada Sahu <barry@native5.com>
 * @copyright 2012 Native5. All Rights Reserved 
 * @license   See attached NOTICE.md for details
 * @version   Release: 1.0 
 * @link      http://www.docs.native5.com 
 * Created : 27-11-2012
 * Last Modified : Fri Dec 21 09:11:53 2012
 */
abstract class ApiClient
{

    protected $_remoteServer;


    /**
     * Default Constructor. 
     * 
     * @access public
     * @return ApiClient 
     * @throws \Exception to handle error
     */
    public function __construct()
    {
        global $app;
        global $logger;
        try {
            $sharedKey = $app->getConfiguration()->getSharedKey();
            $secretKey = $app->getConfiguration()->getSecretKey();
            if (empty($secretKey) || empty($sharedKey)) {
                throw new \Exception('Need shared & secret key, to be able to talk with remote server');
            } 
        } catch (\Exception $e) {
            throw new \Exception('Need shared & secret key, to be able to talk with remote server');
        }
        $signatureOpts              = array();
        $signatureOpts['keyId']     = $sharedKey;
        $signatureOpts['key']       = $secretKey; 
        $signatureOpts['algorithm'] = 'sha1';
        $this->_remoteServer = new Client(ApiConfig::BASE_URL);
        $this->_remoteServer->addSubscriber(new HmacSignaturePlugin($signatureOpts));
        $this->_remoteServer->getEventDispatcher()->addListener(
            'request.error', 
            function (Event $e) {
                if ($event['response']->getStatusCode() >= 400 && $event['response']->getStatusCode() < 500) {
                    $event->stopPropagation();
                    $logger->debug('Incoming Response '.print_r($event['response'], 1));
                    throw new ClientException('Error in data, please recheck inputs.');
                } else if ($event['response']->getStatusCode() >= 500 ) {
                    $event->stopPropagation();
                    throw new ServiceException('We are currently facing some technical issues, please try again in some time.');
                }
            }
        );
    }


    /**
     * setRemoteClient 
     * 
     * @param mixed $client 
     * @access public
     * @return void
     */
    public function setRemoteClient($client) {
        $this->_remoteServer = $client;
    }
}
