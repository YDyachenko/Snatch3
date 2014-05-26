<?php

namespace Application\Model;

class TransactionTemplate
{

    protected $id;
    protected $name;
    protected $user_id;
    protected $from;
    protected $to;
    protected $sum;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = (int) $value;
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
        $this->from = $value;
        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($value)
    {
        $this->to = $value;
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

    public function getArrayCopy()
    {
        return array(
            'id'      => $this->id,
            'user_id' => $this->user_id,
            'name'    => $this->name,
            'from'    => $this->from,
            'to'      => $this->to,
            'sum'     => $this->sum,
        );
    }

    public function exchangeArray($array)
    {
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'name':
                    $this->setName($value);
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
            }
        }

        return $this;
    }

}
