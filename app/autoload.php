<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->add('Zend', __DIR__.'/../vendor/zendframework');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
