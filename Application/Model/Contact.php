<?php

namespace Application\Model;

class Contact
{

    protected $id;
    protected $user_id;
    protected $name;
    protected $account;
    protected $description;

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

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = (string) $value;
        return $this;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function setAccount($value)
    {
        $this->account = round($value, 2);
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
        return $this;
    }

    public function getArrayCopy()
    {
        return array(
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'name'        => $this->name,
            'account'     => $this->account,
            'description' => $this->description,
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
                case 'name':
                    $this->setName($value);
                    break;
                case 'account':
                    $this->setAccount($value);
                    break;
                case 'description':
                    $this->setDescription($value);
                    break;
            }
        }

        return $this;
    }

}
