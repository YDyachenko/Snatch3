<?php

namespace Application\Model;

class Transaction
{

    protected $id;
    protected $user_id;
    protected $from;
    protected $to;
    protected $sum;
    protected $otp_code;
    protected $confirmed;
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

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($value)
    {
        $this->from = (int) $value;
        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($value)
    {
        $this->to = (int) $value;
        return $this;
    }

    public function getSum()
    {
        return $this->sum;
    }

    public function setSum($value)
    {
        $this->sum = round($value, 2);
        return $this;
    }

    public function getOtpCode()
    {
        return $this->otp_code;
    }

    public function setOtpCode($value)
    {
        $this->otp_code = (string) $value;
        return $this;
    }

    public function getConfirmed()
    {
        return $this->confirmed;
    }

    public function setConfirmed($value)
    {
        $this->confirmed = (bool) $value;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = (string) $value;
        return $this;
    }

    public function getArrayCopy()
    {
        return array(
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'from'        => $this->from,
            'to'          => $this->to,
            'sum'         => $this->sum,
            'otp_code'    => $this->otp_code,
            'confirmed'   => $this->confirmed,
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
                case 'from':
                    $this->setFrom($value);
                    break;
                case 'to':
                    $this->setTo($value);
                    break;
                case 'sum':
                    $this->setSum($value);
                    break;
                case 'otp_code':
                    $this->setOtpCode($value);
                    break;
                case 'confirmed':
                    $this->setConfirmed($value);
                    break;
                case 'description':
                    $this->setDescription($value);
                    break;
            }
        }

        return $this;
    }

}
