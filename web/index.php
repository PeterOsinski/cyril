<?php

use Symfony\Component\ClassLoader\ApcClassLoader;

ini_set('display_errors', 0);
error_reporting(0);

$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader = new ApcClassLoader('pofm::', $loader);
$loader->register(true);

new Core\App(false);
