<?php

use \Doctrine\Common\Annotations\AnnotationRegistry;

// Because the components are loaded standalone, doctrine's file loader doesn't map
// the folder structure to the namespace correctly, using the composer autoloader
// handles this correctly.
$autoloadFile = DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR  . 'autoload.php';
$autoloadPath = __DIR__ . $autoloadFile;
if (!file_exists($autoloadPath)) {
    $autoloadPath = dirname(dirname($autoloadPath)) . $autoloadFile;
}
$loader = require $autoloadPath;

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;