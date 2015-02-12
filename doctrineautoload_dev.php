<?php

use \Doctrine\Common\Annotations\AnnotationRegistry;

$baseDir = dirname(__FILE__);
$validatorLocation = "/vendor/symfony/validator/";
// make sure that the doctrine annotation is autoloaded...

AnnotationRegistry::registerAutoloadNamespace("Symfony\Component\Validator", $baseDir . $validatorLocation);

