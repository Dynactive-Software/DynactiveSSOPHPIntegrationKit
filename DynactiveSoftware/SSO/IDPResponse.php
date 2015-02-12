<?php
namespace DynactiveSoftware\SSO;

/**
 * Description of IDPResponse
 *
 * @author snielson
 */
class IDPResponse {

    private $relayState;
    private $response;
    private $destination;

    public function setRelayState($relayState) {
        $this->relayState = $relayState;
    }
    public function getRelayState() {
        return $this->relayState;
    }

    public function getResponse() {
        return base64_encode($this->response);
    }
    
    public function getRawResponse() {
        return $this->response;
    }

    public function setResponse($xmlResponse) {
        $this->response = $xmlResponse;
    }
    
    public function getDestination() {
        return $this->destination;
    }

    public function setDestination($destination) {
        $this->destination = $destination;
    }
}