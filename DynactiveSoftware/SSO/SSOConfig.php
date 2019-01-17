<?php
namespace DynactiveSoftware\SSO;

use Symfony\Component\Validator\Constraints as Assert;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Configuration file for SSO.  Note the private key and public certificate must be set.
 * The private key should be encoded as RSA_SHA1 (see xmlseclibrary) and the public certificate
 * should be using RSA_1_5 (see xmlseclibrary).  If these are not valid files and encoded properly the 
 * config will throw exceptions.
 *
 * @author snielson
 */
class SSOConfig {

    /**
     * The SAML2.0 attribute xml format that each of the user and config attributes will follow.
     */
    const ATTRIBUTE_FORMAT = "urn:oasis:names:tc:SAML:2.0:attrname-format:basic";
    
    /**
     * The user was authenticated through a password protected means.
     */
    const AUTHN_CONTEXT_CLASS = "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport";
    
    /**
     * The length of time an assertion is allowed to be valid (time for it to reach the intended recipient
     * and time for processing.  This allows some time drift but servers should be reasonably up to date).
     */
    const DEFAULT_MAX_ASSERTION_TIME_ALLOWED = "+10 Minutes";
    
    /**
     * The identity provider's private key
     * @Assert\NotBlank(message = "Identity provider key must be set")
     * @var XMLSecurityKey
     */
    private $idpPrivateKey;
    
    /**
     * The source provider's public certificate
     * @Assert\NotBlank(message = "Source provider certificate must be set")
     * @var XMLSecurityKey
     */
    private $spPublicCert;
    
    /**
     * The string contents of the public certificate.  This is inserted into IDP
     * SAML response assertions in order to verify that the assertion came from the IDP
     * @var string
     */
    private $spPublicCertContents;
    
    /**
     * The location where the identity provider should authenticate with the source provider
     * @Assert\NotBlank(message = "Authentication destination for source provider must be set")
     * @Assert\Url(message = "Authentication destination must be a valid URL")
     * @var string
     */
    private $authenticationDestination;
    
    /**
     * The location where the user should be redirected to once the SP has logged in.
     * @var string
     */
    private $redirectOnLoginDestination;
    
    /**
     * The location where the source provider should send users to when they logoff from the
     * source provider location (this is not IDP initiated logoff).
     * @Assert\NotBlank(message = "Logoff destination for identity provider must be set")
     * @Assert\Url(message = "Logoff destination must be a valid URL")
     * @var string
     */
    private $redirectOnLogoffDestination;
    
    /**
     * The location where the source provider should send users to if there are errors in the
     * authentication process
     * @Assert\NotBlank(message = "Error destination for identity provider must be set")
     * @Assert\Url(message = "Error destination must be a valid URL")
     * @var string
     */
    private $redirectOnErrorDestination;
    
    /**
     * The unique entityID (see SAML2 spec) that is issuing the assertion.
     * This is usually a unique URI (such as the company's website) but can be any string.
     * @Assert\NotBlank(message = "issuer must be set and be a unique value for the caller")
     * @return string
     */
    private $issuer;
    
    /**
     * The maximum time an assertion is allowed to be received from it's creation date before
     * the assertion is considered invalid.  
     * @var integer 
     */
    private $maxAssertionTimeAllowed;
    
    /**
     * If the assertion should be encrypted before sending it.  Turning this flag to false
     * enables debugging of the assertion properties.
     * @var boolean
     */
    private $encryptAssertion = true;
    
    public function __construct() {
        $this->maxAssertionTimeAllowed = strtotime(self::DEFAULT_MAX_ASSERTION_TIME_ALLOWED);
        $this->encryptAssertion = true;
    }
        
    /**
     * The identity provider's private key
     * @return XMLSecurityKey
     */
    public function getIdpPrivateKey() {
        return $this->idpPrivateKey;
    }

    /**
     * The source provider's public certificate
     * @return XMLSecurityKey
     */
    public function getSpPublicCert() {
        return $this->spPublicCert;
    }

    /**
     * The location where the identity provider should authenticate with the source provider
     * @return string
     */
    public function getAuthenticationDestination() {
        return $this->authenticationDestination;
    }

    /**
     * Set's the identity provider private key using the given path to the key file on disk.
     * The key should be encrypted using RSA_SHA1 @see XMLSecurityKey::RSA_SHA1
     * @param type $idpPrivateKeyPath A valid filesystem path to the private key.
     * @throws \InvalidArgumentException If the private key path is not a valid path to an encrypted private key
     */
    public function setIdpPrivateKey($idpPrivateKeyPath) {
        
        if(!is_string($idpPrivateKeyPath)){
			throw new \InvalidArgumentException("The private key path must be a string.");
		}
        
        $idpSecurityKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
        $idpSecurityKey->loadKey($idpPrivateKeyPath, true);
        if (!$idpSecurityKey->key) {
            throw new \InvalidArgumentException("$idpPrivateKeyPath was not a valid key");
        }
        $this->idpPrivateKey = $idpSecurityKey;
    }

    /**
     * Set's the source provider's public certificate using the given path to the key file on disk.
     * The key should be encrypted using RSA_1_5 @see XMLSecurityKey::RSA_1_5
     * @param type $idpPrivateKeyPath A valid filesystem path to the private key.
     * @throws \InvalidArgumentException If the private key path is not a valid path to an encrypted private key
     */
    public function setSpPublicCert($spPublicCertPath) {
        if(!is_string($spPublicCertPath)){
			throw new \Exception("The certificate path must be a string.");
		}
        
        $spSecurityKey = new XMLSecurityKey(XMLSecurityKey::RSA_1_5, array('type' => 'public'));
        $spSecurityKey->loadKey($spPublicCertPath, true);
        if (!$spSecurityKey->key) {
            throw new \InvalidArgumentException("$spPublicCertPath was not a valid certificate");
        }
        $this->spPublicCert = $spSecurityKey;
        $this->spPublicCertContents = file_get_contents($spPublicCertPath);
    }
    
    /**
     * Returns the contents of the source provider's public certificate
     * @return string
     */
    public function getSpPublicCertificateContents() {
        return $this->spPublicCertContents;
    }

    /**
     * Sets the source provider URL that the SAML assertion's will be sent to.
     * @param string $authenticationDestination
     */
    public function setAuthenticationDestination($authenticationDestination) {
        $this->authenticationDestination = $authenticationDestination;
    }

    /**
     * Get the unique entityID (see SAML2 spec) that is issuing the assertion.
     * This is usually a unique URI but can be any string.
     * @return string
     */
    public function getIssuer() {
        return $this->issuer;
    }

    /**
     * Sets the unique entityID (see SAML2 spec) that is issuing the assertion.
     * This is usually a unique URI but can be any string.
     * @return string
     */
    public function setIssuer($issuer) {
        $this->issuer = $issuer;
    }
    
    /**
     * The maximum time an assertion is allowed to be received from it's creation date before
     * the assertion is considered invalid.  
     * @return integer
     */
    public function getMaxAssertionTimeAllowed() {
        return $this->maxAssertionTimeAllowed;
    }

    /**
     * 
     * @param integer $maxAssertionTimeAllowed
     */
    public function setMaxAssertionTimeAllowed($maxAssertionTimeAllowed) {
        $this->maxAssertionTimeAllowed = $maxAssertionTimeAllowed;
    }
    
    /**
     * The location where the user should be redirected to once the SP has logged in.
     * @return string
     */
    public function getRedirectOnLoginDestination() {
        return $this->redirectOnLoginDestination;
    }

    /**
     * Sets the URL that the user agent should be redirected to once they have
     * authenticated at the source provider.  This is normally passed through the
     * RelayState parameter.
     * @param string $loginDestination
     */
    public function setRedirectOnLoginDestination($loginDestination) {
        $this->redirectOnLoginDestination = $loginDestination;
    }
    
    /**
     * The location where the source provider should send users to when they logoff from the
     * source provider location (this is not IDP initiated logoff). 
     * @return string
     */
    public function getRedirectOnLogoffDestination() {
        return $this->redirectOnLogoffDestination;
    }
    
    /**
     * Set the location where the source provider should send users to when they logoff from the
     * source provider location (this is not IDP initiated logoff). 
     * @param string $logoffDestination
     */
    public function setRedirectOnLogoffDestination($logoffDestination) {
        $this->redirectOnLogoffDestination = $logoffDestination;
    }

    
    /**
     * The location where the source provider should send users to if there are errors in the
     * authentication process 
     * @return string
     */
    public function getRedirectOnErrorDestination() {
        return $this->redirectOnErrorDestination;
    }

    /**
     * Set the location where the source provider should send users to if there are errors in the
     * authentication process
     * @param string $errorDestination
     */
    public function setRedirectOnErrorDestination($errorDestination) {
        $this->redirectOnErrorDestination = $errorDestination;
    }
    
        /**
     * Sets the assertions to be encrypted.
     */
    public function enableAssertionEncryption() {
        $this->encryptAssertion = true;
    }
    
    /**
     * Turns off the encryption of the assertion, which can be useful for debugging.
     */
    public function disableAssertionEncryption() {
        $this->encryptAssertion = false;
    }
    
    /**
     * Returns if the assertion sent to the SP should be encrypted
     * @return boolean
     */
    public function shouldEncryptAssertion() {
        return $this->encryptAssertion;
    }

    
    public function getSSOAttributes() {
        $attributes = array();
        
        // add the logoff, login, and error roles
        if ($this->getRedirectOnLoginDestination() != null) {
            $attributes['redirectOnLogin'] = $this->getRedirectOnLoginDestination();
        }
        
        if ($this->getRedirectOnLogoffDestination() != null) {
            $attributes['redirectOnLogoff'] = $this->getRedirectOnLogoffDestination();
        }
        
        if ($this->getRedirectOnErrorDestination() != null) {
            $attributes['redirectOnError'] = $this->getRedirectOnErrorDestination();
        }
        
        return $attributes;
    }
}
