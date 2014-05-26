<?php
error_reporting(E_ALL);
ini_set('display_errors', 'stdout');

chdir(dirname(__DIR__));

include 'library/Core/Loader/StandardAutoloader.php';

$loader = new Core\Loader\StandardAutoloader();
$loader->registerNamespace('Application', './Application')
       ->register();

$application = new Core\Mvc\Application();
$application->run();