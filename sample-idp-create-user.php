<?php
include "vendor/autoload.php";

use DynactiveSoftware\LearningPlatform\LMSUser;
use DynactiveSoftware\LearningPlatform\LMSConfig;
use DynactiveSoftware\LearningPlatform\SSOHandler;

$cwd = getcwd();
$certPath = $cwd . DIRECTORY_SEPARATOR . "certs" . DIRECTORY_SEPARATOR . "sample" . DIRECTORY_SEPARATOR;
$config = new LMSConfig();
try {
    
    $config->setAuthenticationDestination("http://localhost:8888/lms/");
    $config->setIdpPrivateKey($certPath . "test.pem");
    $config->setSpPublicCert($certPath . "../dynactives.pem");
    $config->setIssuer("http://www.dynactivesoftware.com/");
    $config->setRedirectOnErrorDestination("http://www.dynactivesoftware.com/error-handler");
    $config->setRedirectOnLogoffDestination("http://www.dynactivesoftware.com/logout");
    $config->setResponseUrl("http://localhost:8000/sample-idp-redirect.php");
}
catch (Exception $ex) {
    // if the config threw an exception we need to check the keys
    echo $ex;
    exit;
}

$user = new LMSUser();
$user->setFirstName("John");
$user->setLastName("Smith");
$user->setEmail("test@dynactivesoftware.com");
$user->setSSOUID("2");
$user->setRole("STUDENT");
$user->setCourseAccessList(array(5000));

try {
    $ssoHandler = new SSOHandler();
    $response = $ssoHandler->createSSOUser($user, $config);
    var_dump($response);
}
catch (Exception $ex) {
    // we missed a configuration option or something is setup incorrectly
    echo $ex;
    exit;
}