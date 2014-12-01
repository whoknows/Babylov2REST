<?php

namespace Models;

class User
{

    public static $stdData = array(
        'id' => 0,
        'enabled' => 0,
        'gravatar' => '',
        'roles' => array(),
        'username' => 'N/A',
        'gameData' => array(
            'played' => 0,
            'won' => 0,
            'lost' => 0,
            'total' => 0,
            'playedThisMonth' => 0,
            'wonThisMonth' => 0,
            'lostThisMonth' => 0,
            'totalThisMonth' => 0
        )
    );

    public static function getFullList($filter = "")
    {
        $data = array();
return self::doUsersQuery($filter);
        foreach (self::doUsersQuery("WHERE enabled = 1") as $userGame) {
            $data[$userGame['id']] = self::makeDataForOneUser($userGame);
        }

        return $data;
    }

    public static function makeDataForOneUser($user)
    {
        $ret = self::$stdData;

        //

        return $ret;
    }

    public static function doUsersQuery($filter = "")
    {
        $sql = "SELECT id, username, email, enabled, SUM(won) as won, COUNT(won) as total, CONCAT(YEAR(date), '.', MONTH(date)) as yearmonth FROM
                (SELECT
                    a.id,
                    username,
                    email,
                    enabled,
                    date,
                    IF((team = 1 AND score_team1 > score_team2) OR (team = 2 AND score_team2 > score_team1), 1, 0) as won
                FROM user a
                INNER JOIN users_games b ON a.id = b.user_id
                INNER JOIN game c ON c.id = b.game_id
                $filter) osef
                GROUP BY id, yearmonth ";

        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->query($sql);

        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getGravatar($email, $s = 40, $d = 'mm', $r = 'x', $img = false, $atts = array())
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }

        return $url;
    }
}
