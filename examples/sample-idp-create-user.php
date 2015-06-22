<?php
include "../vendor/autoload.php";
include "config.php";

use DynactiveSoftware\LearningPlatform\LMSUser;
use DynactiveSoftware\LearningPlatform\SSOHandler;
use DynactiveSoftware\LearningPlatform\LMSRole;

// defined in config.php
$config = getLMSConfig();
// uncomment this line if you want to see what is being sent as the raw assertion 
// will be unencrypted.  Note the server is expecting an encrypted response
// so this cannot be used for actual user creation.
//$config->disableAssertionEncryption();

$user = new LMSUser();
// unique user id in your system
$user->setSSOUID(uniqid());
$user->setFirstName("John");
$user->setLastName("Smith");

// must be a valid email with a TLD extension
$user->setEmail("test@dynactivesoftware.com");

// the type of user this is. See LMSRole for more explanation on the individual roles.
$user->setRole(LMSRole::Student);
//$user->setRole(LMSRole::Instructor);
//$user->setRole(LMSRole::ClientAdmin);

// comment this line out if you are a ClientAdmin
// the identifiers of the courses you want this user to have access to.
// These are the Project ID numbers that come from the Deployed Courses section 
// at SOURCE_PROVIDER_AUTHENTICATION_LOCATION . "/#t=Home&c=Deploy Course"
// each array value must be a valid int
// Note this should be ALL of the courses that this user has purchased, or been 
// given access to for the LMS
$user->setCourseAccessList(array(5000001));

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