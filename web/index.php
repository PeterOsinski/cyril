<?php

use Symfony\Component\ClassLoader\ApcClassLoader;

$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader = new ApcClassLoader('pofm::', $loader);
$loader->register(true);

ini_set('display_errors', 0);
error_reporting(0);

new Core\App(false);
