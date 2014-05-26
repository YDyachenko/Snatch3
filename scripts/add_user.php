<?php

chdir(dirname(__DIR__));

use Core\ServiceManager\ServiceManager;
use Core\Config\Config;

/* Init autoload */

include 'library/Core/Loader/StandardAutoloader.php';

$loader = new Core\Loader\StandardAutoloader();
$loader->registerNamespace('Application', './Application')
        ->register();

/* Init ServiceManager */

$smConfig       = include 'configs/serviceManager.php';
$serviceManager = new ServiceManager();

foreach ($smConfig['factories'] as $name => $factory) {
    $serviceManager->setFactory($name, $factory);
}

$cfgArray = include 'configs/main.php';
$serviceManager->set('config', new Config($cfgArray));

$db = $serviceManager->get('db');

$login     = 'participant';
$names = array_map('trim', file('scripts/names.dict'));

for ($i = 0; $i < 20; $i++) {
    $password  = generateTanCode(10);
    
    $name = $names[array_rand($names)];
    $name = explode(' ', $name);

    $query = "INSERT INTO `users` VALUES(NULL,?,?,0,'tan',NULL,'',?,?,'')";
    $db->query($query, $login . ($i+1), md5($password), $name[0], $name[1]);

    $userId = $db->lastInsertId();

    $db->query("UPDATE `users` set card_id = id, email = concat('user', id, '@ibank.phd') WHERE id = ?", $userId);

    $accountRub = "90107430600227300" . sprintf("%03u", $userId);
    $accountUsd = "80107430600227300" . sprintf("%03u", $userId);

    $query = "INSERT INTO `accounts` VALUES(NULL,?,?,?,?)";
    $db->query($query, $userId, $accountRub, 0, 'rub');
    $db->query($query, $userId, $accountUsd, 0, 'usd');

    echo "Login: " . $login . ($i+1) . PHP_EOL;
    echo "Password: " . $password . PHP_EOL;
    echo "Codes: " . PHP_EOL;
    
    $tans = [];
    $query = "INSERT INTO `tan` VALUES(?, NULL, ?, 0)";
    for ($j = 1; $j <= 20; $j++) {
        $tans[$j] = generateTanCode();
        echo "$j. {$tans[$j]}\n";
        $db->query($query, $userId, $tans[$j]);
    }

//    var_export($tans);
    echo PHP_EOL;

    $db->query("INSERT INTO `rel_users_services` VALUES (?, 3)", $userId);
    $db->query("INSERT INTO `rel_users_services` VALUES (?, 1)", $userId);
}

function generateTanCode($length = 5)
{
    $charset = '0123456789';
    $code    = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $charset[rand(0, 9)];
    }

    return $code;
}
