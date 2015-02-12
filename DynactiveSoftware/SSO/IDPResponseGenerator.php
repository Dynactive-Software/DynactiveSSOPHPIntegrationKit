<?php

namespace DynactiveSoftware\SSO;

use DynactiveSoftware\SSO\SSOConfig;
use DynactiveSoftware\SSO\SSOUser;
use DynactiveSoftware\SSO\IDPResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use SAML2_Response;
use SAML2_Compat_ContainerSingleton;
use SAML2_EncryptedAssertion;

use DOMDocument;

/**
 * Creates an IDPResponse which contains the encoded SAML response to be used in
 * the HTTP Post Binding for IDP initiated login.
 *
 * @author snielson
 */
class IDPResponseGenerator {
    
    /**
     * The constraints validator
     * @var ValidatorInterfacoe
     */
    private $validator;
    
    /**
     * Compatability container for SAML2 in working with the simplesaml libraries.
     * @var SAML2_Compat_AbstractContainer
     */
    private $samlCompatabilityContainer;
    
    public function __construct($samlCompatabilityContainer = null) {
        
        if ($samlCompatabilityContainer != null && $samlCompatabilityContainer instanceof \SAML2_Compat_AbstractContainer) {
            $this->samlCompatabilityContainer = $samlCompatabilityContainer;
        }
        else {
            $this->samlCompatabilityContainer = new SAML2CompatContainer();
        }
        
        $this->validator = Validation::createValidatorBuilder()
                ->enableAnnotationMapping()
                ->getValidator();
    }
    
    /**
     * Given an sso user and sso config generate the SAML response to be delivered
     * to the HTTP client for the HTTP Post Binding.
     * @return IDPResponse
     */
    public function getResponse(SSOUser $user, SSOConfig $config) {
        
        // set our compatability container singleton... urgh bad library interface
        SAML2_Compat_ContainerSingleton::setContainer($this->samlCompatabilityContainer);
        
        $this->validateUser($user);
        $this->validateConfig($config);
        
        $response = new SAML2_Response();
        $response->setIssuer($config->getIssuer());
        $response->setDestination($config->getAuthenticationDestination());
        $response->setId(uniqid());
        $response->setCertificates(array($config->getSpPublicCertificateContents()));
        
        // sign it with our private key so the sourceProvider can verify it came from us.
        $response->setSignatureKey($config->getIdpPrivateKey());
        
        // issue instance is the time we created this response
        $response->setIssueInstant(time());
        
        $assertion = new \SAML2_Assertion();
        
        // we are using basic number/string values for our attributes
        $assertion->setAttributeNameFormat(SSOConfig::ATTRIBUTE_FORMAT);
        
        // this test hasn't really authenticated by password protection, but our normal IDP's will.
        $assertion->setAuthnContextClassRef(SSOConfig::AUTHN_CONTEXT_CLASS);
        
        $assertion->setNotBefore(time()); // don't allow it before this current time.
        $assertion->setNotOnOrAfter($config->getMaxAssertionTimeAllowed()); // set them to time out in 10 minutes
        
        // now add all of the user information
       
        $userAttributes = $this->createAttributes($user, $config);
        $samlAttributes = $this->translateAttributes($userAttributes);
        $assertion->setAttributes($samlAttributes);
        
        // in SAML IDP-Initiated relay state is the location to send the user to
        // once they have logged in.
        $response->setRelayState($config->getRedirectOnLoginDestination());
        
        // now encrypt it using the sourceProvider's public key so that only they can decrypt it
        $encryptedAssertion = new SAML2_EncryptedAssertion();
        $encryptedAssertion->setAssertion($assertion, $config->getSpPublicCert());
        
        $response->setAssertions(array($encryptedAssertion));
        
        return $this->createIDPResponse($response, $config);
    }
    
    /**
     * Verifies that all of the required fields in the config are set
     * @param SSOConfig $config
     */
    protected function validateConfig(SSOConfig $config) {

        $violations = $this->validator->validate($config);
        if (count($violations) > 0) {
            throw new SSOValidationException("SSOConfig properties are invalid", $violations);
        }
    }
    
    /**
     * Verifies that all of the required attributes are 
     * @param SSOUser $user
     */
    protected function validateUser(SSOUser $user) {
        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            throw new SSOValidationException("SSOUser properties are invalid", $violations);
        }
    }
    
    protected function createAttributes(SSOUser $user, SSOConfig $ssoConfig) {
        $reflection = new \ReflectionClass($user);
        $properties = $reflection->getProperties();
        $attributes = array();
        foreach ($properties as $value) {
            $value->setAccessible(true);
            $userProperties[$value->getName()] = $value->getValue($user);
            $value->setAccessible(false);
        }
        
        // add the logoff, login, and error roles
        $attributes['redirectOnLogin'] = $ssoConfig->getRedirectOnLoginDestination();
        $attributes['redirectOnLogoff'] = $ssoConfig->getRedirectOnLogoffDestination();
        $attributes['redirectOnError'] = $ssoConfig->getRedirectOnErrorDestination();
        
        return $attributes;
    }
    
    /**
     * Takes an array of attributes and converts them into a format appropriate
     * for SAML
     * @param array $attributes
     */
    private function translateAttributes(array $attributes) {
        // not sure why but saml requires everything to be an array
        $attributes = array_map(function($item) {
            if (is_array($item)) {
                return $item;
            }
            // TODO: stephen find out why dateCreated behaves differently here..
            else if ($item instanceof \DateTime) {
                return $item;
            }
            return array($item);
        }, $attributes);
        
        return $attributes;
    }
    
    /**
     * Given the SAML2_Response create our entity object that contains the response
     * to be output.
     * @param \SAML2_Response $response
     * @return IDPResponse
     */
    private function createIDPResponse(\SAML2_Response $response, SSOConfig $config) {
        
        $doc = new DomDocument();
        $newNode = $doc->importNode($response->toSignedXML(), true);
        $xml = $doc->saveXML($newNode);
        
        $idpResponse = new IDPResponse();
        $idpResponse->setResponse($xml);
        $idpResponse->setRelayState($response->getRelayState());
        $idpResponse->setDestination($response->getDestination());
        return $idpResponse;
    }
}
