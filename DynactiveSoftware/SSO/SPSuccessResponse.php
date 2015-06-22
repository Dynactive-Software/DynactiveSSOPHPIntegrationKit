<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DynactiveSoftware\SSO;

use DynactiveSoftware\SSO\SPResponse;

/**
 * Description of SPSuccessResponse
 *
 * @author snielson
 */
class SPSuccessResponse extends SPResponse {
    private $ssoUid;
    
    public function __construct() {
        $this->setStatus(self::STATUS_SUCCESS);
    }
    
    public function setSsoUid($ssoUid) {
        $this->ssoUid = $ssoUid;
    }
    
    public function getSsoUid() {
        return $this->ssoUid;
    }
    
    public function __toString() {
        return "status=" . $this->getStatus() . ",ssoUid=" . $this->getSsoUid();
    }
}
