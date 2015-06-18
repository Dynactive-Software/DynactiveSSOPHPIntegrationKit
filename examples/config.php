<?php

define ("SOURCE_PROVIDER_AUTHENTICATION_LOCATION", "http://localhost:8888/lms/");

define ("IDENTITY_PROVIDER_ROOT_SITE", "http://localhost:8000/");

define ("IDENTITY_PROVIDER_ISSUER", IDENTITY_PROVIDER_ROOT_SITE);

define ("IDENTITY_PROVIDER_ERROR_DESTINATION", IDENTITY_PROVIDER_ROOT_SITE ."sample-idp-error-handler.php");

define ("IDENTITY_PROVIDER_LOGOFF_DESTINATION", IDENTITY_PROVIDER_ROOT_SITE ."sample-idp-logoff.php");

define ("IDENTITY_PROVIDER_RESPONSE_URL", IDENTITY_PROVIDER_ROOT_SITE ."sample-idp-redirect.php");

$cwd = getcwd();
$certPath = $cwd . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "certs" . DIRECTORY_SEPARATOR;

define ("SOURCE_PROVIDER_PUBLIC_CERTIFICATE", $certPath . "dynactives.crt");

define ("IDENTITY_PROVIDER_PRIVATE_KEY", $certPath . "sample" . DIRECTORY_SEPARATOR . "test.pem");

function getLMSConfig() {
    $config = new DynactiveSoftware\LearningPlatform\LMSConfig();
    try {

        $config->setAuthenticationDestination(SOURCE_PROVIDER_AUTHENTICATION_LOCATION);
        $config->setIdpPrivateKey(IDENTITY_PROVIDER_PRIVATE_KEY);
        $config->setSpPublicCert(SOURCE_PROVIDER_PUBLIC_CERTIFICATE);
        $config->setIssuer(IDENTITY_PROVIDER_ISSUER);
        $config->setRedirectOnErrorDestination(IDENTITY_PROVIDER_ERROR_DESTINATION);
        $config->setRedirectOnLogoffDestination(IDENTITY_PROVIDER_LOGOFF_DESTINATION);
        $config->setResponseUrl(IDENTITY_PROVIDER_RESPONSE_URL);
    }
    catch (Exception $ex) {
        // if the config threw an exception we need to check the keys
        echo $ex;
        exit;
    }
    return $config;
}