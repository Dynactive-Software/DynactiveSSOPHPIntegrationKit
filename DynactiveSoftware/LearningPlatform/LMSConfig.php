<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DynactiveSoftware\LearningPlatform;

/**
 * Description of LMSConfig
 *
 * @author snielson
 */
class LMSConfig extends \DynactiveSoftware\SSO\SSOConfig {
    
    private $responseUrl;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getSSOAttributes() {
        $attributes = parent::getSSOAttributes();

        if ($this->getRedirectOnLogoffDestination() != null) {
            // slight mismatch because this is what gaerdvark expects... unfortunately
            unset($attributes['redirectOnLogoff']);
            $attributes['redirectOnLogout'] = $this->getRedirectOnLogoffDestination();
        }
        
        if (isset($this->responseUrl)) {
            $attributes['responseUrl'] = $this->getResponseUrl();
        }

        return $attributes;
    }
    
    public function setResponseUrl($url) {
        $this->responseUrl = $url;
    }
    
    public function getResponseUrl() {
        return $this->responseUrl;
    }
}