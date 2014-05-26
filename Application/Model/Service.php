<?php

namespace Application\Model;

class Service
{

    protected $id;
    protected $name;

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

    public function getArrayCopy()
    {
        return array(
            'id'   => $this->id,
            'name' => $this->name,
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
            }
        }

        return $this;
    }

}
