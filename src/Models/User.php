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
        'email' => '',
        'gameData' => array(
            'played' => 0,
            'won' => 0,
            'lost' => 0,
            'given' => 0,
            'taken' => 0,
            'total' => 0,
            'playedThisMonth' => 0,
            'wonThisMonth' => 0,
            'lostThisMonth' => 0,
            'givenThisMonth' => 0,
            'takenThisMonth' => 0,
            'totalThisMonth' => 0,
            'playedLastMonth' => 0,
            'wonLastMonth' => 0,
            'lostLastMonth' => 0,
            'givenLastMonth' => 0,
            'takenLastMonth' => 0,
            'totalLastMonth' => 0
        )
    );

    public static function getFullList($filter = "")
    {
        $data = array();

        foreach (Game::getUsersGameData($filter) as $userGame) {
            if (!isset($data[$userGame['id']])) {
                $data[$userGame['id']] = self::makeDataForOneUser($userGame);
            }

            self::incrementGameData($data[$userGame['id']]['gameData'], $userGame);
            self::setTotals($data[$userGame['id']]['gameData']);
            self::getComplementaryData($data[$userGame['id']]['gameData'], $userGame['id']);
        }

        return $data;
    }

    public static function makeDataForOneUser($user)
    {
        $ret = self::$stdData;

        $ret['id'] = $user['id'];
        $ret['username'] = $user['username'];
        $ret['email'] = $user['email'];
        $ret['gravatar'] = self::getGravatar($user['email']);
        $ret['enabled'] = $user['enabled'];
        $ret['roles'] = explode(',', Roles::getRoles("WHERE user_id = " . $user['id']));

        return $ret;
    }

    public static function incrementGameData(&$gameData, $game)
    {
        $k = '';
        if(date('Y.m', strtotime('last month')) == $game['yearmonth']){
            $k = 'LastMonth';
        } elseif (date('Y.m') == $game['yearmonth']) {
            $k = 'ThisMonth';
        }

        $gameData['won' . $k] += $game['won'];
        $gameData['lost' . $k] += ($game['total'] - $game['won']);
        $gameData['played' . $k] += $game['total'];
        $gameData['given' . $k] += $game['given'];
        $gameData['taken' . $k] += $game['taken'];

        if ($k != '') {
            $gameData['won'] += $game['won'];
            $gameData['lost'] += ($game['total'] - $game['won']);
            $gameData['played'] += $game['total'];
            $gameData['given'] += $game['given'];
            $gameData['taken'] += $game['taken'];
        }
    }

    public static function setTotals(&$gameData)
    {
        $total = Game::getTotalGames();

        $gameData['total'] = array_sum($total);
        $gameData['totalThisMonth'] = isset($total[date('Y.m')]) ? $total[date('Y.m')] : 0;
        $gameData['totalLastMonth'] = isset($total[date('Y.m', strtotime('last month'))]) ? $total[date('Y.m', strtotime('last month'))] : 0;
    }

    public static function getComplementaryData(&$gameData, $id)
    {
        $conditions = array(
            'bestOponent' => "IF(ug.team = 1, ug2.team = 2 AND score_team1 < score_team2, ug2.team = 1 AND score_team1 > score_team2)",
            'worstOponent' => "IF(ug.team = 1, ug2.team = 2 AND score_team1 > score_team2, ug2.team = 1 AND score_team1 < score_team2)",
            'bestMate' => "IF(ug.team = 1, ug2.team = 1 AND score_team1 > score_team2, ug2.team = 2  AND score_team1 < score_team2)",
            'worstMate' => "IF(ug.team = 1, ug2.team = 1 AND score_team1 < score_team2, ug2.team = 2  AND score_team1 > score_team2)"
        );

        foreach (array('', 'ThisMonth', 'LastMonth') as $period) {
            foreach ($conditions as $field => $where) {
                if($period != ''){
                    $osef = $period == 'ThisMonth' ? 'now' : 'last month';
                    $where .= " AND g.date BETWEEN '" . date('Y-m-01', strtotime($osef)) . " 00:00:00' AND '" . date('Y-m-t', strtotime($osef)) . " 00:00:00'";
                }
                $sql = "SELECT u.id
                        FROM users_games ug
                        INNER JOIN game g ON g.id = ug.game_id
                        INNER JOIN users_games ug2 ON ug2.game_id = g.id AND ug2.user_id != ug.user_id
                        INNER JOIN user u ON u.id = ug2.user_id
                        WHERE ug.user_id = $id AND $where
                        GROUP BY ug2.user_id
                        ORDER BY COUNT(ug.id) DESC
                        LIMIT 0,1";
                $req = \Config\Database::getInstance()->getConnection()->query($sql);
                $res = $req->fetch(\PDO::FETCH_ASSOC);
                $gameData[$field . $period] = intval($res['id']);
            }
        }
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

    public static function getUniqueUser($users)
    {
        if (sizeof($users) == 1) {
            return array_values($users)[0];
        }

        return null;
    }

    public static function isUserConnected($app)
    {
        if ($app->getCookie('currentUser')) {
            return json_decode($app->getCookie('currentUser'));
        }

        return isset($_SESSION['currentUser']) ? $_SESSION['currentUser'] : null;
    }

    public static function doLoginAction($login, $password, $app)
    {
        $bdd = \Config\Database::getInstance()->getConnection();
        $login = $bdd->quote($login);
        $password = $bdd->quote($password);
        $user = User::getUniqueUser(self::getFullList("WHERE username = $login AND password = $password"));

        if ($user !== null) {
            $_SESSION['currentUser'] = $user;
            $app->setCookie('currentUser', json_encode($user), '30 day');
        } else {
            $user = array('message' => 'Mauvais login/mot de passe.');
        }

        return $user;
    }

    public static function doLogoutAction($app)
    {
        $_SESSION['currentUser'] = null;
        $app->deleteCookie('currentUser');

        return null;
    }

    public static function post($user)
    {
        $bdd = \Config\Database::getInstance()->getConnection();
        $sql = "INSERT INTO user VALUES (default, :username, :password, :enabled, :email)";

        try {
            $sth = $bdd->prepare($sql);
            $sth->execute(array(
                ':email' => isset($user['email']) ? $user['email'] : '',
                ':username' => ucfirst($user['username']),
                ':enabled' => isset($user['enabled']) ? $user['enabled'] : '0',
                ':password' => $user['password']
            ));

            $user['id'] = $bdd->lastInsertId();

            self::insertRoles($user);
        } catch (\Exception $e) {
            return $e->getTrace();
        }

        return $user['id'];
    }

    public static function put($user)
    {
        $bdd = \Config\Database::getInstance()->getConnection();
        $sql = "UPDATE user SET email = :email, username = :username, enabled = :enabled WHERE id = :id";

        $sth = $bdd->prepare($sql);
        $sth->execute(array(
            ':email' => $user['email'],
            ':username' => $user['username'],
            ':enabled' => $user['enabled'],
            ':id' => $user['id']
        ));

        if (isset($user['password'])) {
            $sql = "UPDATE user SET password = :pass WHERE id = :id";

            $sth = $bdd->prepare($sql);
            $sth->execute(array(
                ':pass' => $user['password'],
                ':id' => $user['id']
            ));
        }

        self::insertRoles($user);

        return $user['id'];
    }

    public static function delete($user)
    {
        $bdd = \Config\Database::getInstance()->getConnection();

        $sql = "DELETE FROM user WHERE id = :id";
        $sth = $bdd->prepare($sql);
        $sth->execute(array('id' => $user['id']));

        $sql = "DELETE FROM users_roles WHERE user_id = :id";
        $sth = $bdd->prepare($sql);
        $sth->execute(array('id' => $user['id']));

        return $user['id'];
    }

    public static function insertRoles($user)
    {
        $bdd = \Config\Database::getInstance()->getConnection();

        if (isset($user['admin']) && isset($user['id'])) {
            $sql = "DELETE FROM users_roles WHERE user_id = :id";
            $sth = $bdd->prepare($sql);
            $sth->execute(array(':id' => $user['id']));
        }

        if (isset($user['roles'])) {
            foreach ($user['roles'] as $role) {
                $sql = "INSERT INTO users_roles VALUES(:user_id, (SELECT id FROM roles WHERE name = :role))";
                $sth = $bdd->prepare($sql);
                $sth->execute(array(':user_id' => $user['id'], ':role' => $role));
            }
        }
    }

}
