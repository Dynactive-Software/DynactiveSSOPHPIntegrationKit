<?php
include "config.php";

use DynactiveSoftware\LearningPlatform\LMSUser;
use DynactiveSoftware\LearningPlatform\SSOHandler;
use DynactiveSoftware\LearningPlatform\LMSRole;

// defined in config.php
$config = getLMSConfig();
// uncomment this line if you want to see what is being sent as the raw assertion 
// will be unencrypted.  Note the server is expecting an encrypted response
// so this cannot be used for actual user authentication.
//$config->disableAssertionEncryption();

// set to this to false if you want to have time to see the assertion before it's sent to the server
$autoSubmitForm = true;

$user = new LMSUser();
// unique user id in your system
$user->setSSOUID("1");
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
// at $config->getAuthenticationDestination() . "/#t=Home&c=Deploy Course"
// each array value must be a valid int
// Note this should be ALL of the courses that this user has purchased, or been 
// given access to for the LMS
$user->setCourseAccessList(array(5000001));

// if you want the user to go directly to a course instead of their LMS dashboard
// you would pass in the course id you want them to go to here.
//$user->setGoDirectToCourse(5000001);

try {
    $ssoHandler = new SSOHandler();
    $idpResponse = $ssoHandler->getAuthenticationResponse($user, $config);
}
catch (Exception $ex) {
    // we missed a configuration option or something is setup incorrectly
    echo $ex;
    exit;
}
?>
<html>
<script>
function submitSaml()
{
	document.forms["saml"].submit();
}
</script>
<body <?php if ($autoSubmitForm) { ?> onload="submitSaml()" <?php } ?> >
<?php 
// for debugging purposes you can comment out the onload in the body and uncomment this line
//	echo htmlentities($idpResponse->getRawResponse()); 
?>
    <form id="saml" name="saml" action="<?php echo $config->getAuthenticationDestination(); ?>" method="POST">
	<input type="hidden" name="SAMLResponse" value="<?php echo $idpResponse->getResponse(); ?>" />
    <input type="hidden" name="saml" value="<?php echo $idpResponse->getResponse(); ?>" />
    <input type="hidden" name="mode" value="authenticate" />
	Please wait while we send you onto the application.
	<input type="submit" name="send" value="Submit" />
</form>
</body>
</html>