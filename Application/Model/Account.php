<?php

namespace Application\Model;

class Account
{

    protected $id;
    protected $user_id;
    protected $number;
    protected $balance;
    protected $currency;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = (int) $value;
        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($value)
    {
        $this->user_id = (int) $value;
        return $this;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($value)
    {
        $this->number = (string) $value;
        return $this;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($value)
    {
        $this->balance = round($value, 2);
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($value)
    {
        $this->currency = $value;
        return $this;
    }

    public function getArrayCopy()
    {
        return array(
            'id'       => $this->id,
            'user_id'  => $this->user_id,
            'number'   => $this->number,
            'balance'  => $this->balance,
            'currency' => $this->currency,
        );
    }
    
    public function exchangeArray($array)
    {
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'user_id':
                    $this->setUserId($value);
                    break;
                case 'number':
                    $this->setNumber($value);
                    break;
                case 'balance':
                    $this->setBalance($value);
                    break;
                case 'currency':
                    $this->setCurrency($value);
                    break;
            }
            
        }
        
        return $this;
    }

}
