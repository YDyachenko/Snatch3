<?php

namespace Application\Service;

use Core\Db\Adapter as DbAdapter;
use Application\Model\User;
use Application\Model\Account;
use Application\Model\Contact;
use Application\Model\Service;
use Core\Mvc\Exception\ForbiddenException;

class UserService
{

    protected $db;

    public function setDbAdapter(DbAdapter $db)
    {
        $this->db = $db;

        return $this;
    }

    public function fetchById($id)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE id = ? ", $id);
        if (!$sth->rowCount())
            return false;

        $user = new User();
        $user->exchangeArray($sth->fetch());

        return $user;
    }
    
    public function fetchByLogin($login)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE login = ? ", $login);
        if (!$sth->rowCount())
            return false;

        $user = new User();
        $user->exchangeArray($sth->fetch());

        return $user;
    }
    
    public function fetchAll($limit = 25, $offset = 0)
    {
        $limit  = (int) $limit;
        $offset = (int) $offset;
        $query  = "SELECT SQL_CALC_FOUND_ROWS * FROM users ORDER BY login LIMIT $offset, $limit";
        $sth    = $this->db->query($query);

        $users = array();

        while ($row = $sth->fetch()) {
            $user = new User();
            $user->exchangeArray($row);

            $users[] = $user;
        }

        $sth = $this->db->query("SELECT FOUND_ROWS()");


        return array(
            'users' => $users,
            'count' => $sth->fetchColumn(),
        );
    }

    public function fetchUserAccounts(User $user)
    {
        $sth = $this->db->query("SELECT * FROM accounts WHERE user_id = ? ", $user->getId());
        if (!$sth->rowCount())
            return array();
        
        $accounts = array();
        
        while($item = $sth->fetch()) {
            $account = new Account();
            $account->exchangeArray($item);
            
            $accounts[$account->getId()] = $account;
        }

        return $accounts;
    }
    
    public function fetchAccountById($id)
    {
        $sth = $this->db->query("SELECT * FROM accounts WHERE id = ? ", $id);
        if (!$sth->rowCount())
            return false;
        
        $account = new Account();
        $account->exchangeArray($sth->fetch());
        
        return $account;
    }
    
    public function fetchAccountByNumber($number)
    {
        $sth = $this->db->query("SELECT * FROM accounts WHERE number = ? ", $number);
        if (!$sth->rowCount())
            return false;
        
        $account = new Account();
        $account->exchangeArray($sth->fetch());
        
        return $account;
    }
    
    public function sendOtp(User $user, $code)
    {
        /** @TODO Don't forget to send message */
        
        return $this;
    }
    
    public function fetchUserServices(User $user)
    {
        $services = array();
        
        $sql = "SELECT id, name FROM services as s, rel_users_services as r " .
               "WHERE r.service_id = s.id AND r.user_id = ?";
        
        $sth = $this->db->query($sql, $user->getId());
        
        while($item = $sth->fetch()) {
            $service = new Service();
            $service->exchangeArray($item);
            
            $services[] = $service;
        }
        
        return $services;
    }
    
    public function isMobileBankAllowed(User $user)
    {
        $sql = "SELECT 1 FROM rel_users_services WHERE user_id = ? AND service_id = 1";
        $sth = $this->db->query($sql, $user->getId());
        
        return $sth->rowCount() ? true : false;
    }
    
    public function fetchIncomingTransfers(User $user)
    {
        $query = "SELECT th.id, at.number as `to`, af.number as `from`, af.currency, th.description, th.sum, th.date FROM `accounts` as at, `accounts` as af, `transactions_history` as th WHERE at.user_id = ? and th.to = at.id and th.from = af.id and shown = 0";
        $sth   = $this->db->query($query, $user->getId());
        $items = array ();
        $ids   = array ();

        while($item = $sth->fetch()) {
            $items[] = $item;
            $ids[] = $item['id'];
        }
        
        if (count($ids)) {
            $this->db->query("UPDATE `transactions_history` SET shown = 1 WHERE id IN(" . implode(",", $ids) . ")");
        }
        
        return $items;
    }

}