<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$em = new Core\Doctrine\DoctrineFactory(true, __DIR__.'/../../Cache/Doctrine', __DIR__.'/../../Application/Entity');

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
            'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em->get())
        ));