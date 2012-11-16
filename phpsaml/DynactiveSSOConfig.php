<?php


class DynactiveSSOConfig{
	
	private $ssoAttributeVals = array();
	private $certificate = null;
	private $key = null;
	private $nameId = null;
	private $issuer = null;
	private $nameQualifier = null;
	
	function __construct($keyPath = "", $certificatePath = "", $issuer, $userUid = "", $firstName="", $lastName = "", $email="", $courseIds = array(), $responseUrl=""){
		$this->setKeyPath($keyPath);
		$this->setCertificatePath($certificatePath);
		$this->setUserUId($userUid);
		$this->setFirstName($firstName);
		$this->setLastName($lastName);
                $this->setIssuer($issuer);
		if(!empty($email)){
			$this->setEmail($email);
		}
		$this->setCourseAccess($courseIds);
		if(!empty($responseUrl)){
			$this->setResponseUrl($responseUrl);
		}
	}
	
	private function setConfigVal($name, $value){
		$this->ssoAttributeVals[$name] = $value;
	}
	
	/**
	 * The path to the key file. (Required)
	 * @param type $keyPath 
	 */
	function setKeyPath($keyPath){
		if(!is_string($keyPath)){
			throw new Exception("The key path must be a string.");
		}
		
		$this->key = $keyPath;
	}
	/**
	 *
	 * @return String
	 */
	function getKeyPath(){
		return $this->key;
	}
	/**
	 * The path to the certificate file.
	 * @param string $certificatePath 
	 */
	function setCertificatePath($certificatePath){
		if(!is_string($certificatePath)){
			throw new Exception("The certifiate path must be a string.");
		}
		$this->certificate = $certificatePath;
	}
	/**
	 * 
	 * @return string
	 */
	function getCertificatePath(){
		return $this->certificate;
	}
	/**
	 * The issuer is the web site that is issuing the identity.
	 * This should be the url of the home page of your site. 
	 * AKE http://www.YourSite.com
	 * @param string $issuer 
	 */
	function setIssuer($issuer){
		if(!is_string($issuer)){
			throw new Exception("The issuer must be a string.");
		}
		$this->issuer = $issuer;
	}
	/**
	 *
	 * @return string your website
	 */
	function getIssuer(){
		return $this->issuer;
	}
	/**
	 * The name id is the name of your company or organization
	 * "Your Company LLC"
	 * @param String $nameId 
	 */
	function setNameId($nameId){
		if(!is_string($nameId)){
			throw new Exception("The certifiate path must be a string.");
		}
		$this->nameId = $nameId;
	}
	/**
	 * 
	 * returns String
	 */
	function getNameId(){
		return $this->nameId;
	}
	/**
	 * This is a name qualifier. Not sure what it does
	 * "Your Company"
	 * @param String $nameQualifier 
	 */
	function setNameQualifier($nameQualifier){
		if(!is_string($nameQualifier)){
			throw new Exception("The name qualifier must be a string.");
		}
		$this->nameQualifier = $nameQualifier;
	}
	/**
	 * 
	 * @return String || null if not defined
	 */
	function getNameQualifier(){
		return $this->nameQualifier;
	}
	/**
	 * Provide a user unique id. This is used when a user changes personal information
	 * such as an email address or a name. (required) 
	 * @param String $userId 
	 */
	function setUserUId($userUid){
		if(!is_string($userUid)){
			throw new Exception("The user unique id must be a string.");
		}
		$this->setConfigVal("userUid", $userUid);
	}
	/**
	 * Returns the user unique id.
	 * @return String || null
	 */
	function getUserUId(){
		if(array_key_exists("userId", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals["userId"];
		}else{
			return null;
		}
	}
	/**
	 * The display Name of a user. (Optional)
	 * @param String $displayName 
	 */
	function setDisplayName($displayName){
		if(!is_string($displayName)){
			throw new Exception("The certifiate path must be a string.");
		}
		$this->setConfigVal("displayName", $displayName);
	}
	/**
	 *
	 * @return String || null if not set
	 */
	function getDisplayName(){
		if(array_key_exists("displayName", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals['displayName'];
		}
		return null;
	}
	/**
	 * The first name of the user (Required)
	 * @param String $firstName 
	 */
	function setFirstName($firstName){
		if(!is_string($firstName)){
			throw new Exception("The first name must be a string.");
		}
		$this->setConfigVal("firstName", $firstName);
	}
	/**
	 * The first name 
	 * @return String
	 */
	function getFirstName(){
		if(array_key_exists("firstName", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals["firstName"];
		}
		return null;
	}
	/**
	 * The last name of the user (Required)
	 * @param type $lastName 
	 */
	function setLastName($lastName){
		if(!is_string($lastName)){
			throw new Exception("The last name must be a string.");
		}
		$this->setConfigVal("lastName", $lastName);
	}
	/**
	 * returns the last name
	 * @return String
	 */
	function getLastName(){
		if(array_key_exists("lastName", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals['lastName'];
		}
		return null;
	}
	/**
	 * The role of the person that you are logging in. (Optional)
	 * Defaults to student if none is provided. 
	 * 
	 * Possible Values are STUDENT, CLIENT_ADMIN, INSTRUCTOR
	 * 
	 * @param String $role 
	 */
	function setRole($role){
		if(!is_string($role)){
			throw new Exception("The role must one of the following strings:" . implode(", ", $roles) );
		}
		$roles = array("STUDENT", "CLIENT_ADMIN", "INSTRUCTOR");
		if(!in_array(strtoupper($role), $roles) ){
			throw new Exception("The role you are trying to set is invalid. You must be one of the following: " . implode(", ", $roles) );
		}
		
		$this->setConfigVal("role", $role);
	}
	/**
	 * return String of the role.
	 */
	function getRole(){
		if(array_key_exists("role", $this->ssoAttributeVals)){
			$this->ssoAttributeVals["role"];
		}
		return null;
	}
	/**
	 * The valid email address of the user. (required)
	 * @param String $email 
	 */
	function setEmail($email){
		if(!$this->isValidEmail($email)){			
			throw new Exception("The email must be a string and formatted correctly.");
		}
		$this->setConfigVal("email", $email);
	}
	/**
	 *
	 * @return String
	 */
	function getEmail(){
		if(array_key_exists("email", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals["email"];
		}
		return null;
	}
	/**
	 * An array of ids that the user has access to. (required)
	 * @param array $courseIds 
	 */
	function setCourseAccess($courseIds){
		if(!is_array($courseIds)){
			throw new Exception("The course ids must be an array of ints.");
		}else{
			foreach($courseIds as $id){
				if(!is_int($id)){
					throw new Exception("The course ids must be an array of ints.");
				}
			}
		}
		
		$this->setConfigVal("courseAccessList", $courseIds );
	}
	/**
	 * returns array
	 */
	function getCourseAccess(){
		if(array_key_exists("courseAccessList", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals["courseAccessList"];
		}
		return null;
	}
	/**
	 * This is the url of where we should send the response to. 
	 * must be a valid url. (required)
	 * @param String $url 
	 */
	function setResponseUrl($url){
		if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
			throw new Exception("The response url must be valid");
		}
		$this->setConfigVal("responseUrl", $url);
	}
	/**
	 *
	 * @return type 
	 */
	function getResponseUrl(){
		if(array_key_exists("responseUrl", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals["responseUrl"];
		}
		return null;
	}
	/**
	 * 
	 * @param int $goDirectToCourse the id of the course/project that you want to go directly to.
	 */
	function setGoDirectToCourse($goDirectToCourse){
		if(!is_int($goDirectToCourse)){
			throw new Exception("Go Direct To Course value must be an integer. It is the course Id that you want to go directly to.");
		}
		$this->setConfigVal("goDirectToCourse", $goDirectToCourse);
	}
	/**
	 *
	 * @return int 
	 */
	function getGoDirectToCourse(){
		if(array_key_exists("goDirectToCourse", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals["goDirectToCourse"];
		}
		return null;
	}
	/**
	 * This url is where we redirect to when a user logs out of our system.
	 * @param String $urlToRedirectToOnLogout 
	 */
	function setRedirectOnLogout($urlToRedirectToOnLogout){
		if (filter_var($urlToRedirectToOnLogout, FILTER_VALIDATE_URL) === FALSE) {
			throw new Exception("The url must be valid");
		}
		$this->setConfigVal("redirectOnLogout", $urlToRedirectToOnLogout);
	}
	/**
	 *
	 * @return String
	 */
	function getRedirectOnLogout(){
		if(array_key_exists("redirectOnLogout", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals['redirectOnLogout'];
		}
		return null;
	}
	/**
	 * The url where we will redirect the user to when they log in.
	 * @param String $urlToRedirectToOnLogin 
	 */
	function setRedirectonLogin($urlToRedirectToOnLogin){
		if (filter_var($urlToRedirectToOnLogin, FILTER_VALIDATE_URL) === FALSE) {
			throw new Exception("The url must be valid");
		}
		$this->setConfigVal("redirectOnLogin", $urlToRedirectToOnLogin);
	}
	/**
	 *
	 * @return String
	 */
	function getRedirectOnLogin(){
		if(array_key_exists("redirectOnLogin", $this->ssoAttributeVals)){
			return $this->ssoAttributeVals["redirectOnLogin"];
		}
		return null;
	}
	/**
	 * checks the required fields. If the values are empty then it will 
	 * throw an exception.
	 */
	function checkRequiredFields(){
		$required = array("userUid", "firstName", "lastName", "email", "courseAccessList", "responseUrl");
		$missingvals = array();
		$allisGood = true;
		
		
		foreach($required as $req){
			if( !array_key_exists($req, $this->ssoAttributeVals) || empty( $this->ssoAttributeVals[$req] ) ){
				$missingvals[] = $req;
				$allisGood = false;
			}
		}
		
		if(empty($this->key)){
			$missingvals[] = "key path";
			$allisGood = false;
		}
		
		if(empty($this->certificate)){
			$missingvals[] = "certificate path";
			$allisGood = false;
		}
		
		
		if(!$allisGood){
			throw new Exception("The following values are required " . implode(",", $missingvals));
		}
	}
	
	private function isValidEmail($email){
		return preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email);
	}
	/**
	 * returns array where the key is the name and the value is the value
	 * of the sso data you need to run the Dynactive PHP SimpleSaml script
	 * 
	 * @throws exception if the data is missing required fields
	 * @return array 
	 */
	function getData(){
		$this->checkRequiredFields();
		return $this->ssoAttributeVals;
	}
	
}
?>
