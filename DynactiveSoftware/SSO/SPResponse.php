<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DynactiveSoftware\SSO;

/**
 * Represents a response from the source provider
 *
 * @author snielson
 */
class SPResponse {
    
    /**
     * The raw response we received from the source provider
     * @var string
     */
    private $rawResponse;
    
    /**
     * The message that was sent from the IDP to the source provider
     * @var IDPResponse
     */
    private $idpResponse;
    
    /**
     * The response status from the service provider.
     * @var string
     */
    private $status;
    
    /**
     * Message details of service provider response.
     * @var string
     */
    private $message;
    
    const STATUS_SUCCESS = 'OK';
    const STATUS_ERROR = 'Error';
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getStatus() {
        return $this->status;
    }
   
    public function setRawResponse($rawResponse) {
        $this->rawResponse = $rawResponse;
    }
    
    public function setIDPResponse(IDPResponse $idpResponse) {
        $this->idpResponse = $idpResponse;
    }
    
    public function getIDPResponse() {
        return $this->idpResponse;
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function __toString() {
        return "status=" . $this->getStatus() . ",message=" . $this->getMessage();
    }
}
