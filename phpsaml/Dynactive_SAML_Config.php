<?php
/**
 * Description of Dynactive_SAML_Config
 * 
 * @author Stephen Nielson <snielson@dynactivesoftware.com>
 * @copyright @2012 Dynactive Software
 * @package www
 * @subpackage SAML
 */
class Dynactive_SAML_Config
{
        private $keyLocation;

        private $formPost;

        private $validAudience;

        private $SAMLIssuer;

        public function __construct($keyLocation, $formPost, $validAudience, $issuer)
        {
		$this->keyLocation = $keyLocation;
		$this->formPost = $formPost;
		$this->validAudience = $validAudience;
		$this->SAMLIssuer = $issuer;

        }

        public function getKeyDirectory()
        {
                return $this->keyLocation;
        }
        
        public function getValidAudience(){
            return $this->validAudience;
        }
        
        public function getFormPost(){
            return $this->formPost;
        }

        public function getValidTimeAuthenticated()
        {
            return strtotime("+1 HOUR", time());
        }

        public function getSAMLIssuer()
        {
            return $this->SAMLIssuer;
        }
}
?>
