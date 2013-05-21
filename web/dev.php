<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\ErrorHandler;

ErrorHandler::register();
ExceptionHandler::register();
Debug::enable();
ini_set('display_errors', 1);
error_reporting(E_ALL);

new Core\App(true);