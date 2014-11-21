<?php

//namespace Babylov2\Models;

class UsersSlots extends Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->belongsTo("user_id", "User", "id", array('alias' => 'user'));
        $this->belongsTo("slot_id", "Slot", "id", array('alias' => 'slot'));
    }

}
