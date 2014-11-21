<?php

class User extends Phalcon\Mvc\Model
{

    private $db;

    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "UsersGames",
            "user_id",
            "game_id",
            "Game",
            "id",
            array('alias' => 'games')
        );
    }

    public function getAllUser()
    {
        /*$phql = "SELECT * FROM User ORDER BY username";
        $users = $this->db->executeQuery($phql);

        $data = array();
        foreach ($users as $user) {
            $data[] = array(
                'id' => $user->id,
                'name' => $user->username,
            );
        }*/

    }

    public function setDb($db)
    {
        $this->db = $db;

        return $this;
    }
}
