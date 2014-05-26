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

define('START_USER', 1);
define('END_USER', 18);

/* TRUNCATE TABLES */

$db->query("TRUNCATE `transaction_templates`");
$db->query("TRUNCATE `transactions_history`");
$db->query("TRUNCATE `transactions`");
$db->query("TRUNCATE `accounts`");
$db->query("TRUNCATE `tan`");
$db->query("TRUNCATE `users`");
$db->query("TRUNCATE `rel_users_services`");

$login = 100001;
$names = array_map('trim', file('scripts/names.dict'));

/* INSERT BOTS */

for ($userId = START_USER; $userId <= END_USER; $userId++) {
    $name = $names[array_rand($names)];
    $name = explode(' ', $name);
    
    $query = "INSERT INTO `users` VALUES(NULL,?,?,0,'none',NULL,?,?,?,'')";
    $db->query($query, $login, md5('s3cr37P@55w0rdphdays' . generateTanCode()), 'user' . $userId . '@ibank.phd', $name[0], $name[1]);
    
    $account = "90107430600227300" . sprintf("%03u", $userId);
    
    $query = "INSERT INTO `accounts` VALUES(NULL,?,?,?,'rub')";
    $db->query($query, $userId, $account, rand(560, 640));
    
    $login++;
}

/* INSERT USERS FOR BRUTEFORCE */

$passwords = array_map('rtrim', file('scripts/passwords.dict'));

for ($i = 0; $i < 5; $i++,$userId++) {
    $password = $passwords[array_rand($passwords)];
    $name = $names[array_rand($names)];
    $name = explode(' ', $name);
    
    $query = "INSERT INTO `users` VALUES(NULL,?,?,0,'none',NULL,?,?,?,'')";
    $db->query($query, $login, md5($password), 'user' . $userId . '@ibank.phd', $name[0], $name[1]);
    
    $account = "90107430600227300" . sprintf("%03u", $userId);
    
    $query = "INSERT INTO `accounts` VALUES(NULL,?,?,?,'rub')";
    $db->query($query, $userId, $account, rand(200, 300));
    
    $login++;
}

/* INSERT USERS FOR BRUTEFORCE + CARD VULN */

for ($i = 0; $i < 7; $i++,$userId++) {
    $password = $passwords[array_rand($passwords)];
    $name = $names[array_rand($names)];
    $name = explode(' ', $name);
    
    $query = "INSERT INTO `users` VALUES(NULL,?,?,0,'tan',?,?,?,?,'')";
    $db->query($query, $login, md5($password), $userId,'user' . $userId . '@ibank.phd', $name[0], $name[1]);
    
    $account = "90107430600227300" . sprintf("%03u", $userId);
    
    $query = "INSERT INTO `accounts` VALUES(NULL,?,?,?,'rub')";
    $db->query($query, $userId, $account, rand(470, 560));
    
    $query = "INSERT INTO `tan` VALUES(?, NULL, ?, 0)";
    for ($j=0; $j < 20; $j++) {
        $db->query($query, $userId, generateTanCode());
    }
    
    $db->query("INSERT INTO `rel_users_services` VALUES (?, 3)", $userId);
    
    $login++;
}

/* INSERT TEMPLATES */

for ($userId = START_USER; $userId <= END_USER;) {
    for ($i = 0; $i < 6; $i++) {
        $nextUserId = ($i == 5) ? $userId - 5 : $userId + 1;

        $query    = "SELECT af.id as `from`, at.number as `to` FROM `accounts` as af, `accounts` as at WHERE af.user_id = ? AND at.user_id = ?";
        $sth      = $db->query($query, $userId, $nextUserId);
        $accounts = $sth->fetch();

        $query = "INSERT INTO `transaction_templates` VALUES(null,?,?,?,?,?)";
        $db->query($query, $userId, "Bot $userId > $nextUserId", $accounts['from'], $accounts['to'], 25);

        $userId++;
    }
}

function generateTanCode()
{
    $charset = '0123456789';
    $code    = '';

    for ($i = 0; $i < 5; $i++) {
        $code .= $charset[rand(0, 9)];
    }

    return $code;
}
