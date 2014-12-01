<?php

//namespace Babylov2\Models;

class UsersGames extends Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->belongsTo("user_id", "User", "id", array('alias' => 'user'));
        $this->belongsTo("game_id", "Game", "id", array('alias' => 'game'));
    }

}
