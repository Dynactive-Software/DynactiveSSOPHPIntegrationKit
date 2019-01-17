<?php

// attempt to see if we can find our vendor directory
include ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

/**
 * Configures and returns the LMSConfig for the samples.  Change these values
 * to your particular setup.
 * 
 * @return \DynactiveSoftware\LearningPlatform\LMSConfig
 */
function getLMSConfig() {
    /** 
     * This is the unique URL suffix that you have been given by Dynactive 
     **/
    $clientLMS = "dynactivessosandbox";
    
    
    /** 
     * This is your Fully Qualified Domain Name FQDN.  If you are running the 
     * cli php server you can leave this alone 
     **/
    $clientDomainName = "localhost:8000";
    
    
    $cwd = getcwd();
    $certPath = $cwd . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "certs" . DIRECTORY_SEPARATOR;

    /**
     * Path to YOUR private key.
     */
    $identityProviderPrivateKey = $certPath . "sample" . DIRECTORY_SEPARATOR . "idpkey.key";
    
    /**
     * path to the dynactive PUBLIC certificate.  If you store your 
     * keys/certificates in a different location, you will need to change this
     * value
     */
    $sourceProviderPublicCertificate = $certPath . "dynactives.crt";

    /**
     * The location that will be used to authenticate with the LMS
     */
    $sourceProviderAuthenticationLocation = "https://www.dynactiveeducation.com/" . $clientLMS . "/";

    /** 
     * This is your Fully Qualified Domain Name FQDN.  If you are running the cli php server you can leave this alone 
     **/
    $identityProviderRootSite = "http://" . $clientDomainName. "/";
    
    /**
     * Used in identifying who is making the assertion statement, typically this is your FQDN
     */
    $identityProviderIssuer = $identityProviderRootSite;
    
    /**
     * the url that the LMS will send it's response to. 
     * This is used during the authentication portion of the API.  Some clients
     * will make the response and error destination the same url.
     */
    $identityProviderResponseUrl = $identityProviderRootSite ."sample-idp-redirect.php";
    
    /**
     * The URL that the LMS will send errors to if there is a problem in the assertion
     * Note: if we cannot decrypt the response, we do not have access to this value and users will be given
     * an error message on the LMS. Verify that your keys are live and working before sending users through.
     */
    $identityProviderErrorDestination = $identityProviderRootSite ."sample-idp-error-handler.php";
    
    /**
     * The URL that the LMS will send users to when they have logged out of the LMS.
     */
    $identityProviderLogoffDestination = $identityProviderRootSite ."sample-idp-logoff.php";


    $config = new DynactiveSoftware\LearningPlatform\LMSConfig();
    try {

        // used in the SAML verification process
        $config->setIssuer($identityProviderIssuer);
        
        $config->setAuthenticationDestination($sourceProviderAuthenticationLocation);
        $config->setIdpPrivateKey($identityProviderPrivateKey);
        $config->setSpPublicCert($sourceProviderPublicCertificate);
        
        $config->setRedirectOnErrorDestination($identityProviderErrorDestination);
        $config->setRedirectOnLogoffDestination($identityProviderLogoffDestination);
        
        // the url that the LMS will send it's response to.  
        // 
        $config->setResponseUrl($identityProviderResponseUrl);
    }
    catch (Exception $ex) {
        // if the config threw an exception we need to check the keys
        echo $ex;
        exit;
    }
    return $config;
}
