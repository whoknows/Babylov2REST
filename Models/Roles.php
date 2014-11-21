<?php

class Roles extends Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "UsersRoles",
            "role_id",
            "user_id",
            "User",
            "id",
            array('alias' => 'users')
        );
    }
}
