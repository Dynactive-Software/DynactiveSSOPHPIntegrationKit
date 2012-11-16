<?php
require_once 'simplesamlphp/_autoload.php';
require_once 'Dynactive_SAML_Config.php';
require_once 'Dynactive_SAML_Assertion_Response.php';
require_once 'DynactiveSSOConfig.php';

//$formAction = "http://gae.dev:8888/lms/";
$formAction = "http://localhost:8888/lms/";

$issuer = "http://www.lovedesign.com";
$validAudience = "http://www.lovedesign.com";

$config = new Dynactive_SAML_Config("", $formAction, $validAudience,$issuer);
$basePath = getcwd();
$cert = $basePath. DIRECTORY_SEPARATOR . "cert/dynactives.pem";
$key = $basePath . DIRECTORY_SEPARATOR . "cert/test.pem";

$userUid = "1";
$firstName = "Bob";
$lastName = "Jones";
$email = "bob@jones.com";
$courseIds = array(334229, 4, 5, 6);
$responseUrl = "http://sandbox.lo/phpsaml/catcher.php";

$ssoConfig = new DynactiveSSOConfig($key, $cert, $issuer, $userUid, $firstName, $lastName, $email, $courseIds, $responseUrl);

$response = new Dynactive_SAML_Assertion_Response($ssoConfig, $validAudience, $key, $cert);

//$response->addAssertionAttribute("Display Name", "Bob");

// skip encoding for debugging purposes
$samlResponse = $response->getResponse(true);

//$encodedResponse = '';
$encodedResponse = base64_encode($samlResponse);
?>
<html>
<script>
function submitSaml()
{
	window.forms["saml"].submit();
}
</script>
<?php 
//	echo $samlResponse; 
?>
<body onload="" >
<form name="saml" action="<?php echo $formAction; ?>" method="POST">
	<input type="hidden" name="saml" value="<?php echo $encodedResponse; ?>" />
	Please wait while we send you onto your courses.
	<input type="submit" name="blah" value="Submit" />
</form>
</body>
</html>
