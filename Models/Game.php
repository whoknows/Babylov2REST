<?php

class Game extends Phalcon\Mvc\Model
{

    private $db;

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

    public function getAllGame()
    {
        $phql = "SELECT * FROM Game ORDER BY date DESC";
        $games = $this->db->executeQuery($phql);

        $data = array();
        foreach ($games as $game) {
            $data[] = array(
                'id' => $game->id,
                'date' => $game->date,
                'score1' => $game->scoreTeam1
            );
        }

        echo json_encode($data);
    }

    public function setDb($db)
    {
        $this->db = $db;

        return $this;
    }
}
