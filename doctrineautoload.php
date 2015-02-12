<?php

use \Doctrine\Common\Annotations\AnnotationRegistry;

$vendorDir = dirname(dirname(dirname(__FILE__)));
$validatorLocation = "/symfony/validator/";
// make sure that the doctrine annotation is autoloaded...

AnnotationRegistry::registerAutoloadNamespace("Symfony\Component\Validator", $vendorDir . $validatorLocation);

