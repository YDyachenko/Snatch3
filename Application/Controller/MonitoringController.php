<?php

namespace Application\Controller;

use Core\Mvc\Controller\AbstractActionController;

class MonitoringController extends AbstractActionController
{

    public function indexAction()
    {
        $layout = $this->application->getLayout();
        $layout->setLayout('Monitoring');

        $db     = $this->serviceManager->get('db');
        $config = $this->serviceManager->get('config');
        
        $query  = "SELECT u.login, (ar.balance + au.balance * {$config->rates['usd>rub']}) as balance FROM `users` as `u`, `accounts` as `ar`, `accounts` as `au` WHERE u.id=ar.user_id AND ar.currency='rub' AND au.currency='usd' AND u.id=au.user_id AND u.id >= 30 ORDER BY `balance` DESC";
        $sth    = $db->query($query);
        $users  = $sth->fetchAll();

        $sql   = "SELECT SUM(balance) FROM `accounts` WHERE user_id <= 30";
        $other = $db->query($sql)->fetchColumn();

        return array (
            'users' => $users,
            'other' => $other
        );
    }

}
