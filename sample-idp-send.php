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
    $config->setRedirectOnErrorDestination("http://localhost:8000/sample-idp-error-handler.php");
    $config->setRedirectOnLogoffDestination("http://localhost:8000/sample-idp-logoff.php");
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
$user->setSSOUID("15");
$user->setRole("STUDENT");
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