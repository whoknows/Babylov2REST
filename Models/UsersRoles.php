<?php

class UsersRoles extends Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->belongsTo("user_id", "User", "id", array('alias' => 'user'));
        $this->belongsTo("role_id", "Roles", "id", array('alias' => 'role'));
    }

}
