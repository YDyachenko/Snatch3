<?php

namespace Application\Controller;

use Core\Mvc\Controller\AbstractActionController;
use Application\Service\Exception;
use Core\Mvc\Exception\ForbiddenException;
use Application\Model\TransactionTemplate;

class PaymentsController extends AbstractActionController
{

    protected $user;

    /** @var Application\Service\TransactionService */
    protected $tService;

    /** @var Core\Http\Request */
    protected $request;

    public function init()
    {
        $auth = $this->serviceManager->get('auth');
        if (!$auth->isAuthenticated()) {
            $this->redirect('/auth/login');
        }

        $user = $auth->getUser();
        if ($user->getForceChangePassword()) {
            $this->redirect('/auth/changePassword');
        }

        $this->application->getLayout()->setBlock('controller', 'payments');

        $this->user     = $this->serviceManager->get('auth')->getUser();
        $this->tService = $this->serviceManager->get('transactionService');
        $this->request  = $this->serviceManager->get('request');
    }

    public function indexAction()
    {
        list($transactions, $accounts) = $this->tService->fetchUserTransactions($this->user);

        return array(
            'transactions' => $transactions,
            'accounts'     => $accounts
        );
    }

    public function createAction()
    {
        $userService = $this->serviceManager->get('userService');
        $accounts    = $userService->fetchUserAccounts($this->user);
        $return      = array (
            'accounts' => $accounts
        );

        if ($this->request->isPost()) {
            try {
                $from        = $this->request->getPost('from');
                $to          = $this->request->getPost('to');
                $sum         = $this->request->getPost('sum');
                $description = $this->request->getPost('description');

                $accountFrom = $userService->fetchAccountById($from);
                $accountTo   = $userService->fetchAccountByNumber($to);
                
                if (!$accountFrom)
                    throw new \Exception("Sender's account not found");
                if (!$accountTo)
                    throw new \Exception("Recipient's account not found");

                $transaction = $this->tService->createTransaction($this->user, $accountFrom, $accountTo, $sum, $description);

                if ($this->user->getOtpMethod() == 'mtan') {
                    $userService->sendOtp($this->user, $transaction->getOtpCode());
                    $redirect = '/payments/confirmMtan/id/' . $transaction->getId();
                } elseif ($this->user->getOtpMethod() == 'tan') {
                    $redirect = '/payments/confirmTan/id/' . $transaction->getId();
                } else {
                    $redirect = '/payments/commit/id/' . $transaction->getId();
                }

                $this->redirect($redirect);
            } catch (\Exception $e) {
                $return['error'] = $e->getMessage();
            }
        }

        return $return;
    }

    public function confirmTanAction()
    {
        $id          = $this->request->getParam('id');
        $transaction = $this->tService->fetchTransactionById($id);

        if ($this->user->getOtpMethod() != 'tan')
            throw new ForbiddenException();
        
        if (!$transaction)
            throw new Exception\TransactionNotFoundException();

        if ($transaction->getUserId() != $this->user->getId())
            throw new ForbiddenException();

        if ($transaction->getConfirmed())
            $this->redirect('/payments/commit/id/' . $transaction->getId());
        
        $userService = $this->serviceManager->get('userService');
        $accountFrom = $userService->fetchAccountById($transaction->getFrom());
        $accountTo   = $userService->fetchAccountById($transaction->getTo());

        $return = array(
            'transaction' => $transaction,
            'accountFrom' => $accountFrom,
            'accountTo'   => $accountTo
        );

        if ($this->request->isPost()) {
            $cardId = $this->request->getPost('card_id');
            $tan    = $this->tService->fetchLastTan($cardId);
            if ($tan->getCode() == $this->request->getPost('otp')) {
                $tan->setUsed(true);
                $this->tService->updateTan($tan);

                $transaction->setConfirmed(true);
                $this->tService->updateTransaction($transaction);

                $this->redirect('/payments/commit/id/' . $transaction->getId());
            } else {
                $return['error'] = true;
            }
        } else {
            $cardId = $this->user->getCardId();
            $tan    = $this->tService->fetchLastTan($cardId);
        }

        $return['tan'] = $tan;

        return $return;
    }

    public function confirmMtanAction()
    {
        $id          = $this->request->getParam('id');
        $transaction = $this->tService->fetchTransactionById($id);
        
        if ($this->user->getOtpMethod() != 'mtan')
            throw new ForbiddenException();

        if (!$transaction)
            throw new Exception\TransactionNotFoundException();

        if ($transaction->getUserId() != $this->user->getId())
            throw new ForbiddenException();

        if ($transaction->getConfirmed())
            $this->redirect('/payments/commit/id/' . $transaction->getId());

        $userService = $this->serviceManager->get('userService');
        $accountFrom = $userService->fetchAccountById($transaction->getFrom());
        $accountTo   = $userService->fetchAccountById($transaction->getTo());

        $return = array(
            'transaction' => $transaction,
            'accountFrom' => $accountFrom,
            'accountTo'   => $accountTo
        );

        if ($this->request->isPost()) {
            if ($transaction->getOtpCode() == $this->request->getPost('otp')) {
                $transaction->setConfirmed(true);
                $this->tService->updateTransaction($transaction);

                $this->redirect('/payments/commit/id/' . $transaction->getId());
            } else {
                $return['error'] = true;
            }
        }

        return $return;
    }

    public function commitAction()
    {
        $id          = $this->request->getParam('id');
        $transaction = $this->tService->fetchTransactionById($id);

        if (!$transaction || !$transaction->getConfirmed())
            throw new Exception\TransactionNotFoundException();

        if ($transaction->getUserId() != $this->user->getId())
            throw new ForbiddenException();

        $userService = $this->serviceManager->get('userService');
        $accountFrom = $userService->fetchAccountById($transaction->getFrom());
        $accountTo   = $userService->fetchAccountById($transaction->getTo());

        return array(
            'transaction' => $transaction,
            'accountFrom' => $accountFrom,
            'accountTo'   => $accountTo,
        );
    }

    public function processAction()
    {
        $id = $this->request->getParam('id');
        try {
            $this->tService->commitTransaction($id, $this->user);
            $this->redirect('/payments/history');
        } catch (Exception\InsufficientFundsException $e) {
            return array(
                'error' => 'InsufficientFunds'
            );
        }
    }

    public function deleteAction()
    {
        $id = $this->request->getParam('id');

        $this->tService->deleteUserTransaction($this->user, $id);

        $this->redirect('/payments');
    }

    public function templatesAction()
    {
        $userService = $this->serviceManager->get('userService');
        $accounts    = array();

        foreach ($userService->fetchUserAccounts($this->user) as $account) {
            $accounts[$account->getId()] = $account;
        }

        return array(
            'user'      => $this->user,
            'templates' => $this->tService->fetchUserTemplates($this->user),
            'accounts'  => $accounts,
        );
    }

    public function addTemplateAction()
    {
        $userService = $this->serviceManager->get('userService');
        $accounts    = $userService->fetchUserAccounts($this->user);

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

            $template = new TransactionTemplate();
            $template->exchangeArray(array(
                'name' => $this->request->getPost('name'),
                'from' => $fromId,
                'to'   => $this->request->getPost('to'),
                'sum'  => $this->request->getPost('sum'),
            ));

            if ($template->getName() && $template->getFrom() && $template->getTo() && ($template->getSum() > 0)) {
                $this->tService->addUserTemplate($this->user, $template);
                $this->redirect('/payments/templates');
            }
        }

        return array(
            'accounts' => $accounts
        );
    }

    public function editTemplateAction()
    {
        $template = $this->tService->fetchTemplateById($this->request->getParam('id'));

        if (!$template)
            throw new Exception\TransactionTemplateNotFoundException();

        if ($template->getUserId() != $this->user->getId())
            throw new ForbiddenException();

        $userService = $this->serviceManager->get('userService');
        $accounts    = $userService->fetchUserAccounts($this->user);

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

            if ($this->tService->updateTemplate($template)) {
                $this->redirect('/payments/templates');
            }
        }

        return array(
            'template' => $template,
            'accounts' => $accounts,
        );
    }

    public function deleteTemplateAction()
    {
        $id = $this->request->getParam('id');

        $this->tService->deleteUserTemplate($this->user, $id);

        $this->redirect('/payments/templates');
    }

    public function startFromTemplateAction()
    {
        $id          = $this->request->getParam('id');
        $template    = $this->tService->fetchTemplateById($id);
        $userService = $this->serviceManager->get('userService');

        try {
            $accountFrom = $userService->fetchAccountById($template->getFrom());
            $accountTo   = $userService->fetchAccountByNumber($template->getTo());

            if (!$accountFrom)
                throw new \Exception("Sender's account not found");
            if (!$accountTo)
                throw new \Exception("Recipient's account not found");

            $transaction = $this->tService->createTransaction($this->user, $accountFrom, $accountTo, $template->getSum(), "Template: " . $template->getName());
        } catch (\Exception $e) {
            return array('exception' => $e->getMessage());
        }

        if ($this->user->getOtpMethod() == 'mtan') {
            $userService->sendOtp($this->user, $transaction->getOtpCode());
            $redirect = '/payments/confirmMtan/id/' . $transaction->getId();
        } elseif ($this->user->getOtpMethod() == 'tan') {
            $redirect = '/payments/confirmTan/id/' . $transaction->getId();
        } else {
            $redirect = '/payments/commit/id/' . $transaction->getId();
        }

        $this->redirect($redirect);
    }

    public function historyAction()
    {
        $userService  = $this->serviceManager->get('userService');
        $userAccounts = $userService->fetchUserAccounts($this->user);

        list($transactions, $accounts) = $this->tService->fetchTransactionsHistory($userAccounts);

        return array(
            'user'         => $this->user,
            'transactions' => $transactions,
            'accounts'     => $accounts,
        );
    }

}
