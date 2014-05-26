<?php

namespace Application\Model;

class Tan
{

    protected $id;
    protected $card_id;
    protected $code;
    protected $used;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = (int) $value;
        return $this;
    }

    public function getCardId()
    {
        return $this->card_id;
    }

    public function setCardId($value)
    {
        $this->card_id = (int) $value;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($value)
    {
        $this->code = (string) $value;
        return $this;
    }

    public function getUsed()
    {
        return $this->used;
    }

    public function setUsed($value)
    {
        $this->used = (bool) $value;
        return $this;
    }

    public function getArrayCopy()
    {
        return array(
            'id'      => $this->id,
            'card_id' => $this->card_id,
            'code'    => $this->code,
            'used'    => $this->used,
        );
    }

    public function exchangeArray($array)
    {
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'card_id':
                    $this->setCardId($value);
                    break;
                case 'code':
                    $this->setCode($value);
                    break;
                case 'used':
                    $this->setUsed($value);
                    break;
            }
        }

        return $this;
    }

}
