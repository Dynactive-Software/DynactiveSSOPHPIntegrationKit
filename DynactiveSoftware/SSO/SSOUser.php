<?php
namespace DynactiveSoftware\SSO;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * An entity containing all of the user attributes that will be sent as part of
 * the SAML assertion.
 *
 * @author snielson
 */
class SSOUser {
    
    /**
     *
     * @Assert\Length(min = 1)
     * @Assert\NotBlank
     * @var string
     */
    private $firstName;
    
    /**
     *
     * @Assert\Length(min = 1)
     * @Assert\NotBlank
     * @var string
     */
    private $lastName;
    
    /**
     *
     * @Assert\NotBlank
     * @Assert\Email
     * @var string
     */
    private $email;
    
    /**
     * @Assert\Length(min = 1)
     * @Assert\NotBlank
     * @var string
     */
    private $ssoUID;
    
    /**
     * @Assert\NotBlank
     * @var string
     */
    private $role;
    
    public function __construct() {
        
    }
    
    /**
     * @Assert\IsTrue(message = "The user should have a valid role")
     */
    public function isValidRole() {
        
        // TODO: stephen add role validation here.
        return true;
    }
    
    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSsoUID() {
        return $this->ssoUID;
    }

    public function getRole() {
        return $this->role;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setSsoUID($ssoUID) {
        $this->ssoUID = $ssoUID;
    }

    public function setRole($role) {
        $this->role = $role;
    }


}
