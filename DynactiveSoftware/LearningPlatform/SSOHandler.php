<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DynactiveSoftware\LearningPlatform;
use DynactiveSoftware\SSO\SSOUser;
use DynactiveSoftware\SSO\SSOConfig;
use DynactiveSoftware\SSO\IDPResponse;
use DynactiveSoftware\SSO\IDPResponseGenerator;
use DynactiveSoftware\SSO\SPErrorResponse;
use DynactiveSoftware\SSO\SPSuccessResponse;

/**
 * Description of SSOHandler
 *
 * @author snielson
 */
class SSOHandler {
    
    const MODE_CREATE = 'create';
    const MODE_AUTHENTICATE = 'authenticate';
    
    private function sendDataToUrl($data, $url) {


        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
   
    /**
     * Creates the SSO User on the Dynactive System
     * @param \DynactiveSoftware\SSO\SSOUser $user
     * @param \DynactiveSoftware\SSO\SSOConfig $config
     * @return type
     */
    public function createSSOUser(SSOUser $user, SSOConfig $config) {
        $idpResponseGenerator = new IDPResponseGenerator();
        $idpResponse = $idpResponseGenerator->getResponse($user, $config);
        
        // now we send the user over
        $base64Message = $idpResponse->getResponse();
        $data = array('SAMLResponse' => $base64Message, 'saml' => $base64Message,
            'mode' => self::MODE_CREATE);
        $responseRaw = $this->sendDataToUrl($data, $config->getAuthenticationDestination());
        
        $jsonObj = json_decode($responseRaw, true);
        
        if (isset($jsonObj["status"]) && $jsonObj["status"] == "Error") {
            $response = new SPErrorResponse();
        }
        else {
            $response = new SPSuccessResponse();
            $response->setSsoUID($jsonObj["userUid"]);
        }
        $response->setRawResponse($responseRaw);
        $response->setIDPResponse($idpResponse);
        
        if (isset($jsonObj["message"])) {
            $response->setMessage($jsonObj["message"]);
        }
        
        return $response;
    }
    
    /**
     * 
     * @param \DynactiveSoftware\SSO\SSOUser $user
     * @param \DynactiveSoftware\SSO\SSOConfig $config
     * @return \DynactiveSoftware\SSO\IDPResponse
     */
    public function getAuthenticationResponse(SSOUser $user, SSOConfig $config) {
        $idpResponseGenerator = new IDPResponseGenerator();
        $idpResponse = $idpResponseGenerator->getResponse($user, $config);
        
        return $idpResponse;
    }
}
