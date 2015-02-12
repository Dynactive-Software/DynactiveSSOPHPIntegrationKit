<?php
include "vendor/autoload.php";

use DynactiveSoftware\SSO\IDPResponseGenerator;
use DynactiveSoftware\SSO\SSOConfig;
use DynactiveSoftware\SSO\SSOUser;

$cwd = getcwd();
$certPath = $cwd . DIRECTORY_SEPARATOR . "certs" . DIRECTORY_SEPARATOR . "sample" . DIRECTORY_SEPARATOR;
$config = new SSOConfig();
try {
    
    $config->setAuthenticationDestination("http://127.0.0.1:8000/saml/");
    $config->setIdpPrivateKey($certPath . "idpkey.key");
    $config->setSpPublicCert($certPath . "spcert.crt");
    $config->setIssuer("http://www.dynactivesoftware.com/");
    $config->setRedirectOnErrorDestination("http://www.dynactivesoftware.com/error-handler");
    $config->setRedirectOnLogoffDestination("http://www.dynactivesoftware.com/logout");
}
catch (Exception $ex) {
    // if the config threw an exception we need to check the keys
    echo $ex;
    exit;
}

$user = new SSOUser();
$user->setFirstName("John");
$user->setLastName("Smith");
$user->setEmail("test@dynactivesoftware.com");
$user->setSSOUID("1");
$user->setRole("ROLE_STUDENT");

try {
    $idpResponseGenerator = new IDPResponseGenerator();
    $idpResponse = $idpResponseGenerator->getResponse($user, $config);
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
    <!-- old implementations of dynactive SAML SSO relied on the value being in the saml field which
    is not according to spec -->
    <input type="hidden" name="saml" value="<?php echo $idpResponse->getResponse(); ?>" />
	Please wait while we send you onto the application.
	<input type="submit" name="send" value="Submit" />
</form>
</body>
</html>