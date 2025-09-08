<?php

namespace HttpStack\Model;

class UserModel
{
    protected $id;
    protected $name;
    public function __construct($id)
    {
        $this->id = $id;
        $this->name = 'User' . $id;
    }
    public function getName()
    {
        return $this->name;
    }
}
