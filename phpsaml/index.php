<?php
require_once 'simplesamlphp/_autoload.php';
require_once 'Dynactive_SAML_Assertion_Response.php';
require_once 'DynactiveSSOConfig.php';

/** This is the unique URL suffix that you have been given by Dynactive **/
$clientLMS = "dynactivetestsso";

/** This is your Fully Qualified Domain Name FQDN.  If you are running the cli php server you can leave this alone **/
$clientDomainName = "localhost:8000";

$basePath = getcwd();
// path to your PRIVATE key
$key = $basePath . DIRECTORY_SEPARATOR . "cert/test.pem";

// path to the dynactive PUBLIC certificate 
$cert = $basePath. DIRECTORY_SEPARATOR . "cert/dynactives.pem";

// unique user id in your system
$userUid = "1";
$firstName = "Bob";
$lastName = "Jones";
$email = "bob@dynactivesoftware.com";
// valid roles are STUDENT, CLIENT_ADMIN, or INSTRUCTOR
$userRole = "STUDENT";

/** Uncomment for an instructor test **/
/*
$userUid = "2";
$firstName = "Instructor";
$lastName = "Jones";
$email = "instructor@dynactivesoftware.com";
$userRole = "INSTRUCTOR";
*/

/** Uncomment for a client_admin test **/
/*
$userUid = "3";
$firstName = "Admin";
$lastName = "Jones";
$email = "admin@dynactivesoftware.com";
// valid roles are STUDENT, CLIENT_ADMIN, or INSTRUCTOR
$userRole = "CLIENT_ADMIN";
*/



// the identifiers of the courses you want this user to have access to.
// These are the Project ID numbers that come from the Deployed Courses section at $formLocation/#t=Home&c=Deploy Course
// each array value must be a valid int
$courseIds = array(20001);

/**
 * The location that receives the SAML response and then sends the user to the LMS if the response is valid.
**/
$responseUrl = "http://$clientDomainName/catcher.php";

/**
 * The url location that a user should be sent to when they are logged out of the LMS.
 */
$logoutUrl= "http://$clientDomainName/logout.php";


$lmsLocation = "http://dscmslms.appspot.com/$clientLMS/";
$issuer = "http://$clientDomainName";
$validAudience = "http://$clientDomainName";

// more terse syntax but we will walk through the options in each of the setters
//$ssoConfig = new DynactiveSSOConfig($key, $cert, $issuer, $userUid, $firstName, $lastName, $email, $courseIds, $responseUrl);
// required fields are userUid, firstName, lastName, email, courseIds, and responseUrl
$ssoConfig = new DynactiveSSOConfig();
$ssoConfig->setKeyPath($key);
$ssoConfig->setCertificatePath($cert);
$ssoConfig->setUserUId($userUid);
$ssoConfig->setFirstName($firstName);
$ssoConfig->setLastName($lastName);

// used in the SAML verification process
$ssoConfig->setIssuer($issuer);
// must be a valid email with a TLD extension
$ssoConfig->setEmail($email);
$ssoConfig->setCourseAccess($courseIds);
$ssoConfig->setRole($userRole);

// the url that the LMS will send it's response to. @see catcher.php
$ssoConfig->setResponseUrl($responseUrl);

$ssoConfig->setRedirectOnLogout($logoutUrl);

// if you want the user to go directly to a course instead of their LMS dashboard
// you would pass in the course id you want them to go to here.
//$ssoConfig->setGoDirectoToCourse($courseId);

$response = new Dynactive_SAML_Assertion_Response($ssoConfig, $validAudience, $key, $cert);

//$response->addAssertionAttribute("Display Name", "Bob");

// if set to true you can get the raw response to display for debugging purposes
// note that some of the values are encrypted such as the signatures.
$debugResponse = false;
$samlResponse = $response->getResponse($debugResponse);

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
//	echo $samlResponse; 
?>
<form id="saml" name="saml" action="<?php echo $lmsLocation; ?>" method="POST">
	<input type="hidden" name="saml" value="<?php echo $samlResponse; ?>" />
	Please wait while we send you onto your courses.
	<input type="submit" name="send" value="Submit" />
</form>
</body>
</html>
