<?php

namespace Models;

class Game
{

    public static function getAllGames($filter = "", $limit = "")
    {
        $sql = "SELECT
                    game.id, date, score_team1, score_team2, users_games.`user_id`, `team`,
                    comment.comment, comment.user_id as author, comment.created_on, comment.id as comment_id
                FROM game
                INNER JOIN `users_games` ON `game_id` = game.id
                LEFT JOIN comment ON comment.game_id = game.id
                $filter
                ORDER BY date DESC, game.id DESC, users_games.id
                $limit";
        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->query($sql);

        return self::formatGameData($req->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function formatGameData($data)
    {
        $ret = array();

        foreach ($data as $row) {
            if(!isset($ret[$row['id']])){
                $ret[$row['id']]['id'] = $row['id'];
                $ret[$row['id']]['date'] = date('d/m/Y', strtotime($row['date']));
                $ret[$row['id']]['st1'] = $row['score_team1'];
                $ret[$row['id']]['st2'] = $row['score_team2'];
                $ret[$row['id']]['comments'] = array();
            }

            if ($row['comment'] != null) {
                $ret[$row['id']]['comments'][$row['comment_id']] = array(
                    'id' => $row['comment_id'],
                    'comment' => $row['comment'],
                    'author' => $row['author'],
                    'created_on' => $row['created_on']
                );
            }

            if(isset($ret[$row['id']]['p1t'.$row['team']])){
                $ret[$row['id']]['p2t'.$row['team']] = $row['user_id'];
            } else {
                $ret[$row['id']]['p1t'.$row['team']]  = $row['user_id'];
            }
        }

        return $ret;
    }

    public static function getUsersGameData($filter = "", $group = "yearmonth", $full = true)
    {
        $select = self::getSelect($group);
        $fullselect = "";
        if($full){
            $fullselect = "id, username, email, enabled,";
        }

        $sql = "SELECT $fullselect SUM(won) as won, IF(date IS NULL, 0, COUNT(won)) as total, SUM(given) as given, SUM(taken) as taken, $select
                FROM (SELECT
                    a.id,
                    username,
                    email,
                    enabled,
                    date,
                    IF(team IS NOT NULL AND team = 1, score_team1, score_team2) as given,
                    IF(team IS NOT NULL AND team = 2, score_team1, score_team2) as taken,
                    IF(team IS NOT NULL AND ((team = 1 AND score_team1 > score_team2) OR (team = 2 AND score_team2 > score_team1)), 1, 0) as won
                FROM user a
                LEFT JOIN users_games b ON a.id = b.user_id
                LEFT JOIN game c ON c.id = b.game_id
                $filter) osef
                GROUP BY id, $group";

        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->query($sql);

        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getTotalGames($group = "yearmonth", $filter = "")
    {
        $select = self::getSelect($group);
        $sql = "SELECT
                    COUNT(id) as total,
                    $select
                FROM game
                $filter
                GROUP BY $group";

        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->query($sql);

        $ret = array();

        foreach($req->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $ret[$row[$group]] = $row['total'];
        }

        return $ret;
    }

    public static function getSelect($group)
    {
        return $group == "yearmonth" ? "CONCAT(YEAR(date), '.', IF(MONTH(date) < 10, CONCAT('0', MONTH(date)), MONTH(date))) as yearmonth" : "DATE(date) as date";
    }

    public static function post($data)
    {
        $bdd = \Config\Database::getInstance();
        $bdd = $bdd->getConnection();

        $sql = "INSERT INTO game VALUES(default, '" . $data['date'] . "', " . $data['st1'] . ", " . $data['st2'] . ")";
        $bdd->exec($sql);
        $id = $bdd->lastInsertId();

        $sql = "INSERT INTO users_games VALUES (default, " . $data['p1t1'] . ", " . $id . ", 1),
                                               (default, " . $data['p2t1'] . ", " . $id . ", 1),
                                               (default, " . $data['p1t2'] . ", " . $id . ", 2),
                                               (default, " . $data['p2t2'] . ", " . $id . ", 2)";
        $bdd->exec($sql);

        return $id;
    }

    public static function delete($id)
    {
        $sql = "DELETE FROM game WHERE id = $id";
        $bdd = \Config\Database::getInstance();
        $bdd->getConnection()->exec($sql);

        $sql = "DELETE FROM users_games WHERE game_id = $id";
        $bdd = \Config\Database::getInstance();
        $bdd->getConnection()->exec($sql);

        return 'ok';
    }
}
