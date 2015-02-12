<?php
namespace DynactiveSoftware\SSO;

/**
 * Handles validation exceptions of the config and entity classes
 *
 * @author snielson
 */
class SSOValidationException extends \InvalidArgumentException {
    
    /**
     * The array of validation violations that have occurred.
     * @var array
     */
    private $violations;
    
    public function __construct($message, $violations) {
        parent::__construct($message);
        
        $this->violations = $violations;
    }
    
    public function getValidationViolations() {
        return $this->violations;
    }
    
    public function __toString() {
        $string = parent::__toString();
        $violationMessages = array();
        foreach ($this->violations as $violation) {
            $violationMessages[] = $violation->getPropertyPath() . ": " . $violation->getMessage();
        }
        return  "Validation Violations: \n" . implode("\n",$violationMessages) . "\n" . $string;
    }
}
