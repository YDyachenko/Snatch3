<?php

namespace Application\Model;

class TransactionHistory
{

    protected $id;
    protected $from;
    protected $to;
    protected $sum;
    protected $date;
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

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = (string) $value;
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
            'from'        => $this->from,
            'to'          => $this->to,
            'sum'         => $this->sum,
            'date'        => $this->date,
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
                case 'from':
                    $this->setFrom($value);
                    break;
                case 'to':
                    $this->setTo($value);
                    break;
                case 'sum':
                    $this->setSum($value);
                    break;
                case 'date':
                    $this->setDate($value);
                    break;
                case 'description':
                    $this->setDescription($value);
                    break;
            }
        }

        return $this;
    }

}
