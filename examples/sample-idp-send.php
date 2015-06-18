<?php
include "../vendor/autoload.php";
include "config.php";

use DynactiveSoftware\LearningPlatform\LMSUser;
use DynactiveSoftware\LearningPlatform\SSOHandler;
use DynactiveSoftware\LearningPlatform\LMSRole;

// defined in config.php
$config = getLMSConfig();

$user = new LMSUser();
$user->setFirstName("John");
$user->setLastName("Smith");
$user->setEmail("test@dynactivesoftware.com");
$user->setSSOUID("15");
$user->setRole(LMSRole::ClientAdmin);
$user->setCourseAccessList(array(5000));

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
<body onload="submitSaml()" >
<?php 
// for debugging purposes you can comment out the onload in the body and uncomment this line
//	echo htmlentities($idpResponse->getRawResponse()); 
?>
    <form id="saml" name="saml" action="<?php echo $config->getAuthenticationDestination(); ?>" method="POST">
	<input type="hidden" name="SAMLResponse" value="<?php echo $idpResponse->getResponse(); ?>" />
    <input type="hidden" name="mode" value="authenticate" />
	Please wait while we send you onto the application.
	<input type="submit" name="send" value="Submit" />
</form>
</body>
</html>