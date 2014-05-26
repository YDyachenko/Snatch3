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

/* Start transactions */
$tService    = $serviceManager->get('transactionService');
$userService = $serviceManager->get('userService');

$startTime = time();
while (time() < $startTime + 60 * 30) {
    for ($userId = 1; $userId <= 18; $userId++) {
        $user = $userService->fetchById($userId);

        try {
            foreach ($tService->fetchUserTemplates($user) as $template) {
                $accountFrom = $userService->fetchAccountById($template->getFrom());
                $accountTo   = $userService->fetchAccountByNumber($template->getTo());
                $transaction = $tService->createTransaction($user, $accountFrom, $accountTo, $template->getSum(), $template->getName());

                $tService->commitTransaction($transaction->getId(), $user);
                echo "{$accountFrom->getNumber()} > {$accountTo->getNumber()} ({$template->getSum()})\n";
            }

            sleep(rand(0, 5));
        } catch (Exception $e) {
            continue;
        }
    }
    echo "Memory: " . memory_get_usage() . "\n";
}