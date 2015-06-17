<?php
namespace DynactiveSoftware\SSO;

use DynactiveSoftware\SSO\SPResponse;

/**
 * Represents an error from the Source Provider
 *
 * @author snielson
 */
class SPErrorResponse extends SPResponse {
    public function __construct() {
        $this->setStatus(self::STATUS_ERROR);
    }
}
