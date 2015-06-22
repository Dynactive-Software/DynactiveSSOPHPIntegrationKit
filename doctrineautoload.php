<?php

use \Doctrine\Common\Annotations\AnnotationRegistry;

// tell doctrine to use composer's registry, complain to doctrine if you don't like it.
// Because the components are loaded standalone, doctrine's file loader doesn't map
// the folder structure to the namespace correctly, using the composer autoloader
// handles this correctly.
$loader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;