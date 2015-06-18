<?php
include "../vendor/autoload.php";
include "config.php";

use DynactiveSoftware\LearningPlatform\LMSUser;
use DynactiveSoftware\LearningPlatform\LMSConfig;
use DynactiveSoftware\LearningPlatform\SSOHandler;
use DynactiveSoftware\LearningPlatform\LMSRole;


// defined in config.php
$config = getLMSConfig();

$user = new LMSUser();
$user->setFirstName("John");
$user->setLastName("Smith");
$user->setEmail("test@dynactivesoftware.com");
$user->setSSOUID("16");
$user->setRole(LMSRole::Student);
$user->setCourseAccessList(array(5000));

try {
    $ssoHandler = new SSOHandler();
    $response = $ssoHandler->createSSOUser($user, $config);
    echo $response . "\n";
    
    // if you want to know what was sent, uncomment this line
//    echo $response->getIDPResponse()->getRawResponse() . "\n";
}
catch (Exception $ex) {
    // we missed a configuration option or something is setup incorrectly
    echo $ex;
    exit;
}