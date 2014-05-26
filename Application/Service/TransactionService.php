<?php

namespace Application\Service;

use Core\Db\Adapter as DbAdapter;
use Application\Model\User;
use Application\Model\Account;
use Application\Model\Transaction;
use Application\Model\Tan;
use Application\Model\TransactionHistory;
use Application\Model\TransactionTemplate;
use Core\Mvc\Exception\ForbiddenException;

class TransactionService
{
    
    /** @var Core\Db\Adapter */
    protected $db;
    protected $rates;


    public function __construct($rates)
    {
        $this->rates = $rates;
    }

    public function setDbAdapter(DbAdapter $db)
    {
        $this->db = $db;

        return $this;
    }
    
    public function createTransaction(User $user, Account $from, Account $to, $sum, $description)
    {
        if ($from->getUserId() != $user->getId())
            throw new ForbiddenException();
        
        if ($from->getId() == $to->getId())
            throw new \Exception("Usage of same account for recipient and sender is not allowed.");
        
        $sum = round($sum, 2);
        if ($sum < 0.01)
            throw new \Exception("Sum of the transaction can't be less than 0.01");

        
        $otpCode = '';
        if ($user->getOtpMethod() == 'mtan')
            $otpCode = $this->generateMTanCode();

        $confirmed = $user->getOtpMethod() == 'none' ? true : false;
        
        $query = "INSERT transactions VALUES(null,?,?,?,?,?,?,?)";
        $this->db->query($query, $user->getId(), $from->getId(), $to->getId(), $sum, $otpCode, $confirmed, $description);

        $transaction = new Transaction();
        $transaction->exchangeArray(array(
            'id'          => $this->db->lastInsertId(),
            'user_id'     => $user->getId(),
            'from'        => $from->getId(),
            'to'          => $to->getId(),
            'sum'         => $sum,
            'otp_code'    => $otpCode,
            'confirmed'   => $confirmed,
            'description' => $description,
        ));

        return $transaction;
    }

    public function generateMTanCode()
    {
        $charset = '0123456789';
        $code    = '';

        for ($i = 0; $i < 5; $i++) {
            $code .= $charset[rand(0, 9)];
        }

        return $code;
    }
    
    public function fetchLastTan($cardId)
    {
        $tan = $this->_fetchLastTan($cardId);
        if ($tan)
            return $tan;
        
        $this->resetCard($cardId);
        $tan = $this->_fetchLastTan($cardId);
        if ($tan)
            return $tan;
        
        throw new Exception\NoTanAvailableException();
    }
    
    protected function _fetchLastTan($cardId)
    {
        $sql = "SELECT * FROM tan WHERE card_id = ? AND used = 0 ORDER BY id DESC LIMIT 1";
        $sth = $this->db->query($sql, $cardId);
        
        if (!$sth->rowCount())
            return false;
        
        $tan = new Tan();
        $tan->exchangeArray($sth->fetch());
                
        
        return $tan;
    }
    
    public function resetCard($cardId)
    {
        $this->db->query("UPDATE tan SET used = 0 WHERE card_id = ?", $cardId);
        
        return $this;
    }
    
    public function updateTan(Tan $tan)
    {
        $sql = "UPDATE tan SET code = ?, used = ? WHERE id = ? AND card_id = ?";
        $this->db->query($sql, $tan->getCode(), $tan->getUsed(), $tan->getId(), $tan->getCardId());
        
        return $this;
    }
    
    public function fetchTransactionById($id)
    {
        $sth = $this->db->query("SELECT * FROM transactions WHERE id = ?", $id);
        if (!$sth->rowCount())
            return false;
        
        $transaction = new Transaction();
        $transaction->exchangeArray($sth->fetch());
        
        return $transaction;
    }
    
    public function updateTransaction(Transaction $transaction)
    {
        $sql = "UPDATE transactions SET confirmed = ? WHERE id = ?";
        $this->db->query($sql, $transaction->getConfirmed(), $transaction->getId());
        
        return $this;
    }
    
    public function commitTransaction($transactionId, User $user)
    {
        $this->db->beginTransaction();
        
        try {
            $sqlTransaction = "SELECT * FROM transactions WHERE id = ? AND confirmed = 1 FOR UPDATE";
            $sth = $this->db->query($sqlTransaction, $transactionId);
            if (!$sth->rowCount())
                throw new Exception\TransactionNotFoundException();

            $transaction = new Transaction();
            $transaction->exchangeArray($sth->fetch());
            
            if ($transaction->getUserId() != $user->getId())
                throw new ForbiddenException();
            
            $accountFrom = $this->fetchAccountForUpdate($transaction->getFrom());
            $accountTo   = $this->fetchAccountForUpdate($transaction->getTo());

            if ($accountFrom->getBalance() < $transaction->getSum())
                throw new Exception\InsufficientFundsException();
            
            /**
             * @todo Must be removed before sending to participants
             */
            if ($accountFrom->getCurrency() != $accountTo->getCurrency()) {
                $query = "SELECT sum(if(currency = 'rub', balance, balance * {$this->rates['usd>rub']})) as `sum` FROM `accounts`";
                $systemSum = $this->db->query($query)->fetchColumn();
                if ($systemSum >= 19800)
                    throw new \Exception("Currency conversions are forbidden");
            }
            
            $sum         = $transaction->getSum();
            $balanceFrom = round($accountFrom->getBalance() - $sum, 2);
            $k           = $accountFrom->getCurrency() . '>' . $accountTo->getCurrency();
            $sum         = $this->rates[$k] * $sum;
            $balanceTo   = round($accountTo->getBalance() + $sum, 2);

            $query = "UPDATE accounts SET `balance` = ? WHERE id = ?";

            $this->db->query($query, $balanceTo, $transaction->getTo());
            $this->db->query($query, $balanceFrom, $transaction->getFrom());
            
            $this->db->query("DELETE FROM transactions WHERE id = ?", $transactionId);
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
        
        $this->db->commit();
        
        $needShow = ($accountFrom->getUserId() != $accountTo->getUserId());
        
        $this->addTransactionHistory($transaction, $needShow);
    }
    
    protected function fetchAccountForUpdate($id)
    {
        $sth = $this->db->query("SELECT * FROM accounts WHERE id = ? FOR UPDATE", $id);
        $item = $sth->fetch();
        
        $account = new Account();
        $account->exchangeArray($item);
        
        return $account;
    }

    public function addTransactionHistory(Transaction $transaction, $needShow = true)
    {
        $sql = "INSERT INTO transactions_history VALUES(?, ?, ?, ?, NOW(), ?, ?)";
        $this->db->query($sql, $transaction->getId(), $transaction->getFrom(),
                         $transaction->getTo(), $transaction->getSum(),
                         $transaction->getDescription(), !$needShow);
        
        return $this;
    }
    
    public function fetchTransactionsHistory(array $accounts)
    {
        $userAccounts = array();
        foreach ($accounts as $account) {
            $userAccounts[] = $account->getId();
        }
        
        $inExpr = implode(',', $userAccounts);
        
        $sql = "SELECT * FROM transactions_history WHERE `from` IN ($inExpr) OR `to` IN($inExpr) ORDER BY `date` DESC LIMIT 50";
        $sth = $this->db->query($sql, $inExpr, $inExpr);
        
        if (!$sth->rowCount())
            return array(array(), array());
        
        $transactions      = array();
        $accountsForSelect = array();

        while ($item = $sth->fetch()) {
            $transaction = new TransactionHistory();
            $transaction->exchangeArray($item);
            
            $transactions[]      = $transaction;
            $accountsForSelect[] = $transaction->getFrom();
            $accountsForSelect[] = $transaction->getTo();
        }
        
        $accountsForSelect = array_unique($accountsForSelect);
        
        $sql = "SELECT * FROM accounts WHERE id IN(" . implode(',', $accountsForSelect) . ")";
        $sth = $this->db->query($sql);
        
        $accounts = array();
        
        while ($item = $sth->fetch()) {
            $account = new Account();
            $account->exchangeArray($item);
            
            $accounts[$account->getId()] = $account;
        }
        
        return array($transactions, $accounts);
    }
    
    public function fetchUserTransactions(User $user)
    {
        $sql = "SELECT * FROM transactions WHERE user_id = ?";
        $sth = $this->db->query($sql, $user->getId());
        
        if (!$sth->rowCount())
            return array(array(), array());
        
        $transactions      = array();
        $accountsForSelect = array();

        while ($item = $sth->fetch()) {
            $transaction = new Transaction();
            $transaction->exchangeArray($item);
            
            $transactions[]      = $transaction;
            $accountsForSelect[] = $transaction->getFrom();
            $accountsForSelect[] = $transaction->getTo();
        }
        
        $accountsForSelect = array_unique($accountsForSelect);
        
        $sql = "SELECT * FROM accounts WHERE id IN(" . implode(',', $accountsForSelect) . ")";
        $sth = $this->db->query($sql);
        
        $accounts = array();
        
        while ($item = $sth->fetch()) {
            $account = new Account();
            $account->exchangeArray($item);
            
            $accounts[$account->getId()] = $account;
        }
        
        return array($transactions, $accounts);
    }
    
    public function deleteUserTransaction(User $user, $id)
    {
        $transaction = $this->fetchTransactionById($id);
        if (!$transaction) {
            throw new Exception\TransactionNotFoundException();
        }
        
        if ($transaction->getUserId() != $user->getId()) {
            throw new ForbiddenException();
        }
        
        $this->db->query("DELETE FROM transactions WHERE id = ?", $id);
        
        return $this;
    }

    public function fetchUserTemplates(User $user)
    {
        $sth = $this->db->query("SELECT * FROM transaction_templates WHERE user_id = ? ", $user->getId());
        if (!$sth->rowCount())
            return array();

        $templates = array();

        while ($item = $sth->fetch()) {
            $template = new TransactionTemplate();
            $template->exchangeArray($item);

            $templates[] = $template;
        }

        return $templates;
    }
    
    public function fetchTemplateById($id)
    {
        $sth = $this->db->query("SELECT * FROM transaction_templates WHERE id = ? ", $id);
        if (!$sth->rowCount())
            return false;
        
        $template = new TransactionTemplate();
        $template->exchangeArray($sth->fetch());
        
        return $template;
    }

    public function addUserTemplate(User $user, TransactionTemplate $template)
    {
        $this->db->query("INSERT INTO transaction_templates VALUES(null,?,?,?,?,?)",
                         $user->getId(), $template->getName(), $template->getFrom(),
                         $template->getTo(), $template->getSum());
        
        $template->setId($this->db->lastInsertId());

        return $this;
    }
    
    public function updateTemplate(TransactionTemplate $template)
    {
        $this->db->query("UPDATE transaction_templates SET `name` = ?, `from` = ?, `to` = ?, `sum` = ? WHERE `id` = ?",
                         $template->getName(), $template->getFrom(),
                         $template->getTo(), $template->getSum(), $template->getId());
                
        return true;
    }
    
    public function deleteUserTemplate(User $user, $templateId)
    {
        $template = $this->fetchTemplateById($templateId);
        if (!$template) {
            throw new Exception\TransactionTemplateNotFoundException();
        }
        
        if ($template->getUserId() != $user->getId()) {
            throw new ForbiddenException();
        }
        
        $this->db->query("DELETE FROM transaction_templates WHERE id = ?", $templateId);
        
        return $this;
    }

}