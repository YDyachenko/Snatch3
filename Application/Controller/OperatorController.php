<?php

namespace Application\Controller;

use Core\Mvc\Controller\AbstractActionController;
use Application\Service\Exception;
use Core\Mvc\Exception\ForbiddenException;
use Application\Model\TransactionTemplate;

class OperatorController extends AbstractActionController
{

    protected $request;

    public function init()
    {
        $layout = $this->application->getLayout();
        $layout->setLayout("Operator");

        $this->request = $this->serviceManager->get('request');
    }

    public function indexAction()
    {
        $userService = $this->serviceManager->get('userService');
        
        $page = $this->request->getParam('page');
        $page = $page < 1 ? 1 : (int)$page;
        $limit = 20;
        $offset = $limit * ($page - 1);
        
        $result = $userService->fetchAll($limit, $offset);
        
        return array(
            'users' => $result['users'],
            'count' => $result['count'],
            'limit' => $limit,
            'page'  => $page,
            
        );
    }

    public function userInfoAction()
    {
        $userId      = $this->request->getParam('id');
        $userService = $this->serviceManager->get('userService');
        $user        = $userService->fetchById($userId);
        if (!$user)
            throw new Exception\UserNotFoundException();

        $transService = $this->serviceManager->get('transactionService');

        return array(
            'user'      => $user,
            'accounts'  => $userService->fetchUserAccounts($user),
            'services'  => $userService->fetchUserServices($user),
            'templates' => $transService->fetchUserTemplates($user)
        );
    }

    public function editTemplateAction()
    {
        $transService = $this->serviceManager->get('TransactionService');
        $userService  = $this->serviceManager->get('userService');
        $template     = $transService->fetchTemplateById($this->request->getParam('id'));

        if (!$template)
            throw new Exception\TransactionTemplateNotFoundException();

        $user     = $userService->fetchById($template->getUserId());
        $accounts = $userService->fetchUserAccounts($user);

        if ($this->request->isPost()) {
            $fromId = $this->request->getPost('from');
            $found  = false;

            foreach ($accounts as $account) {
                if ($account->getId() == $fromId) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new \Exception("Account not found");
            }

            $template->exchangeArray(array(
                'name' => $this->request->getPost('name'),
                'from' => $fromId,
                'to'   => $this->request->getPost('to'),
                'sum'  => $this->request->getPost('sum'),
            ));

            if ($transService->updateTemplate($template)) {
                $this->redirect('/operator/userInfo/id/' . $template->getUserId());
            }
        }

        return array(
            'template' => $template,
            'accounts' => $accounts,
        );
    }

}
