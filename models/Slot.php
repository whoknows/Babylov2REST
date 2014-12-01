<?php

class Slot extends Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "UsersSlots",
            "slot_id",
            "user_id",
            "User",
            "id",
            array('alias' => 'users')
        );
    }
}
