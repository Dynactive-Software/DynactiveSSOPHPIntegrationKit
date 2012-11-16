<?php
/**
 * Handles all SAML reponse items that have to do with Dynactive.
 * Specifically the class Generates a simplesaml XML assertion response
 * @author Stephen Nielson <snielson@dynactivesoftware.com>
 * @copyright @2012 Dynactive Software LLC.
 * @package www
 * @subpackage SAML
 */


require_once('simplesamlphp/_autoload.php');
//require_once('Dynactive_SAML_Config.php');
require_once('DynactiveSSOConfig.php');

/**
 * This class represents an attribute element that is necessary for
 * the integration with simplesamlphp
 */
class Dynactive_SAMLAttribute
{
    /**
     * List of attributes
     * @var array
     */
    private $attributes = array();

    /**
     * Return the list of attributes
     */
    public function getAttributes(){
        return $this->attributes;
    }

    /**
     * Add a name value pair for the attribute.
     */
    public function setAttribute($name, $value){
        
        if (is_array($value))
        {
            $this->attributes[$name] = $value;
        }
        else
        {
            $val = array($value);
            $this->attributes[$name] = $val;
        }
    }
}


class Dynactive_SAML_Assertion_Response {

    /**
     * List of Dynactive_SAMLAttribute
     * @var array
     */
        private $attributeList;

        /**
         * Location of the certificate file to include inside the assertion
         * response
         * @var string
         */
        private $certificate;

        /**
         * Location of the private key that we use to sign the saml assertion
         * @var string
         */
        private $privateKey;

        /**
         * @var
         */
        private $issuer;

        private $validAudience;

        private $validTimeAuthenticated;

        private $authnContext;

        private $attributeNameFormat;
        
        /**
         *
         * @var DynactiveSSOConfig
         */
        private $samlConfig;

        public function __construct(DynactiveSSOConfig $samlConfig, $validAudience= NULL, $privateKey = NULL, $certificate = NULL)
        {
            // TODO: see if we need to move valid audience to samlConfig
                $this->validAudience = $validAudience;
                
                // TODO: do we want the issuer to be the .com??  Or should we
                // make it the currently logged in domain?
                $this->issuer = $samlConfig->getIssuer();
                
                $this->privateKey = $samlConfig->getKeyPath();
                
                $this->setCertificate($samlConfig->getCertificatePath());
                
                $this->samlConfig = $samlConfig;

                $this->validTimeAuthenticated = strtotime("+1 HOUR", time());
                $this->authnContext = "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport";
                $this->attributeNameFormat = "urn:oasis:names:tc:SAML:2.0:attrname-format:basic";
                $this->attributeList = new Dynactive_SAMLAttribute();
        }

        public function setReceiver($reciever)
        {
                $this->receiver = $receiver;
        }

        public function setCertificate($certificate)
        {
                if (!file_exists($certificate))
                {
                        throw new InvalidArgumentException("Certificate: " . $certificate . " does not exist");
                }

                $this->certificate = $certificate;
        }

        public function addAssertionAttribute($name, $value)
        {
                if ($name == NULL || $value == NULL)
                {
                        throw new InvalidArgumentException("Name and Value cannot be null");
                }

                $this->attributeList->setAttribute($name, $value);
        }

        public function setValidTimeAuthenticated($time)
        {
                $this->validTimeAuthenticated = $time;
        }

        
        /**
         * 
         * @param $skipBase64Encode If set to true the response is returned in it's raw format without base 64 encoding
         * @throws InvalidArgumentException if the DynactiveSSOConfig->checkRequiredFields fails.  @see DynactiveSSOConfig.checkRequiredFields
         * @return type
         */
        public function getResponse($skipBase64Encode=false)
        {
            
            // if this fails let the exception get raised.
                $this->samlConfig->checkRequiredFields();
                
                // add in the configuration attributes.
                $attributes = $this->samlConfig->getData();
                foreach ($attributes as $key => $value)
                {
                    $this->addAssertionAttribute($key, $value);
                }
                
                $response = new SAML2_Response();

                // private key
                $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
                $objKey->loadKey($this->privateKey, TRUE);

                // grab the certificate
                $cert = file_get_contents($this->certificate);

                $response->setSignatureKey($objKey);
                $response->setCertificates(array($cert));

                // now create our assertion;
                $assertion = new SAML2_Assertion();
                $assertion->setIssuer($this->issuer);
                $assertion->setNotBefore(time());
                $assertion->setNotOnOrAfter($this->validTimeAuthenticated);
                $assertion->setValidAudiences(array($this->validAudience));
                $assertion->setSessionNotOnOrAfter(NULL); // if we want the session to time out on their end
                $assertion->setAuthnContext($this->authnContext);
                $assertion->setCertificates(array($cert));
                $assertion->setSignatureKey($objKey); // sign with the same key.
                $assertion->setAttributes($this->attributeList->getAttributes());
                $assertion->setAttributeNameFormat($this->attributeNameFormat);
                
                 // now encrypt it
//                 $response->setAssertions(array($assertion));
                
                // symmetric rsa
                $rsa1_5 = new XMLSecurityKey(XMLSecurityKey::RSA_1_5, array('type'=>'public'));
                $rsa1_5->loadKey($this->certificate, TRUE);
                $encryptedAssertion = new SAML2_EncryptedAssertion();
                $encryptedAssertion->setAssertion($assertion, $rsa1_5);
//                
                $response->setAssertions(array($encryptedAssertion));
 
                $doc = new DomDocument();

                $newNode = $doc->importNode($response->toSignedXML(), true);

                if ($skipBase64Encode)
                {
                    return $doc->saveXML($newNode);
                }
                else
                {
                    return base64_encode($doc->saveXML($newNode));
                }
        }
}
?>
