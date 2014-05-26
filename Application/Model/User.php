<?php

namespace Application\Model;

class User
{

    protected $id;
    protected $login;
    protected $password;
    protected $force_change_password;
    protected $otp_method;
    protected $card_id;
    protected $email;
    protected $first_name;
    protected $last_name;
    protected $phone;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = (int) $value;
        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($value)
    {
        $this->login = (string) $value;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = (string) $value;
        return $this;
    }

    public function getForceChangePassword()
    {
        return $this->force_change_password;
    }

    public function setForceChangePassword($value)
    {
        $this->force_change_password = (bool) $value;
        return $this;
    }

    public function getOtpMethod()
    {
        return $this->otp_method;
    }

    public function setOtpMethod($value)
    {
        $this->otp_method = (string) $value;
        return $this;
    }
    
    public function getCardId()
    {
        return $this->card_id;
    }

    public function setCardId($value = null)
    {
        $this->card_id = $value;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = (string) $value;
        return $this;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function setFirstName($value)
    {
        $this->first_name = (string) $value;
        return $this;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function setLastName($value)
    {
        $this->last_name = (string) $value;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($value)
    {
        $this->phone = (string) $value;
        return $this;
    }

    public function getArrayCopy()
    {
        return array(
            'id'                    => $this->id,
            'login'                 => $this->login,
            'password'              => $this->password,
            'force_change_password' => $this->force_change_password,
            'otp_method'            => $this->otp_method,
            'card_id'               => $this->card_id,
            'email'                 => $this->email,
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'phone'                 => $this->phone,
        );
    }

    public function exchangeArray($array)
    {
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'login':
                    $this->setLogin($value);
                    break;
                case 'password':
                    $this->setPassword($value);
                    break;
                case 'force_change_password':
                    $this->setForceChangePassword($value);
                    break;
                case 'otp_method':
                    $this->setOtpMethod($value);
                    break;
                case 'card_id':
                    $this->setCardId($value);
                    break;
                case 'email':
                    $this->setEmail($value);
                    break;
                case 'first_name':
                    $this->setFirstName($value);
                    break;
                case 'last_name':
                    $this->setLastName($value);
                    break;
                case 'phone':
                    $this->setPhone($value);
                    break;
            }
        }

        return $this;
    }

}
