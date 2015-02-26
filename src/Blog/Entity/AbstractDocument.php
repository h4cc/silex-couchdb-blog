<?php

namespace Blog\Entity;

abstract class AbstractDocument
{
    private $id;
    private $rev;

    abstract public function getType();

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRev()
    {
        return $this->rev;
    }

    /**
     * @param mixed $rev
     */
    public function setRev($rev)
    {
        $this->rev = $rev;
    }

    public function toArray()
    {
        return array(
            'type' => $this->getType()
        );
    }

    public function fromArray(array $data)
    {
        if ($data['_rev']) {
            $this->setRev($data['_rev']);
        }
        if ($data['_id']) {
            $this->setId($data['_id']);
        }
    }
}