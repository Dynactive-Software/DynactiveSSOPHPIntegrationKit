<?php
namespace DynactiveSoftware\LearningPlatform;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * Represents a user in the Learning Platform System
 *
 * @author snielson
 */
class LMSUser extends \DynactiveSoftware\SSO\SSOUser {
    
    /**
     * @Assert\All({
     *  @Assert\NotBlank,
     *  @Assert\Type(type="integer"),
     *  @Assert\GreaterThan(value=0)
     * })
     * @var array
     */
    private $courseAccessList;
    
    /**
     * @Assert\Length(min = 1)
     * @Assert\NotBlank
     * @var string
     */
    private $userUid;
    
    /**
     * The course id to log a user into directly.  Optional field.
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value=0)
     * @var integer 
     */
    private $goDirectToCourse;
    
     /**
     * @Assert\Length(min = 1)
     * @var string
     */
    private $displayName;
    
    public function __construct() {
        parent::__construct();
        $this->courseAccessList = array();
    }
    
    /**
     * 
     * @param type $ssoUID
     */
    public function setSsoUID($ssoUID) {
        parent::setSsoUID($ssoUID);
        $this->userUid = $ssoUID;
    }
    
    public function setUserUid($uid) {
        $this->setSsoUID($uid);
    }
    
    public function getDisplayName() {
        return $this->displayName;
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }

    public function getUserUid() {
        return $this->userUid;
    }
    
    public function clearCourseAccessList() {
        $this->courseAccessList = array();
    }
    
    public function setCourseAccessList(array $courseAccessList) {
        $this->clearCourseAccessList();
        foreach ($courseAccessList as $courseId) {
            $this->addCourseAccess($courseId);
        }
    }
    
    public function getCourseAccessList() {
        return $this->courseAccessList;
    }
    
    public function addCourseAccess($courseId) {
        if (!is_int($courseId)) {
            throw new \InvalidArgumentException("courseId must be a valid integer, $courseId was given");
        }
        $this->courseAccessList[] = $courseId;
    }
    
    public function removeCourseAccess($courseId) {
        $index = array_search($courseId, $this->courseAccessList);
        if ($index < 0) {
            throw new \InvalidArgumentException("courseId $courseId was not found in course access list");
        }
        $this->courseAccessList = array_splice($this->courseAccessList, $index);
    }
    
    public function getGoDirectToCourse() {
        return $this->goDirectToCourse;
    }

    public function setGoDirectToCourse($goDirectToCourse) {
        $this->goDirectToCourse = $goDirectToCourse;
    }


}
