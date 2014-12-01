<?php

class Game extends Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "UsersGames",
            "game_id",
            "user_id",
            "User",
            "id",
            array('alias' => 'users')
        );
    }
}
