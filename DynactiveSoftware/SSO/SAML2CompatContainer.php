<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DynactiveSoftware\SSO;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Description of SAML2CompatContainer
 *
 * @author snielson
 */
class SAML2CompatContainer extends \SAML2_Compat_AbstractContainer {
    private $logger;
    
    public function __construct($logger = null) {
        
        if ($logger != null && $logger instanceof \Psr\Log\LoggerInterface) {
            $this->logger = $logger;
        }
        else {
            $this->logger = new Logger('saml2');
            $this->logger->pushHandler(new StreamHandler('saml.log', Logger::WARNING));
        }
    }
    
    public function debugMessage($message, $type) {
        if ($message instanceof \DOMElement) {
            $document = new \DOMDocument();
            $document->importNode($message, true);
            $nodeMessage = $document->saveHTML();
            $logMsg = "phpsimplesaml type: $type. message: $nodeMessage";
        }
        else {
            $logMsg = "phpsimplesaml type: $type. message: $message";
        }
        
        $this->logger->debug($logMsg);
    }

    public function generateId() {
        return uniqid();
    }

    public function getLogger() {
        return $this->logger;
    }

    public function postRedirect($url, $data = array()) {
        // we don't use this functionality so it is a no-op for now
    }

    public function redirect($url, $data = array()) {
        // again we aren't telling the user to redirect on a get post so we no-op
        // this for now.
    }

//put your code here
}
