<?php

error_reporting(-1);

$autoloader = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloader)) {
    throw new Exception(
        'Please run "composer install" in root directory to setup unit test dependencies before running the tests'
    );
}

require_once $autoloader;

unset($autoloader);
