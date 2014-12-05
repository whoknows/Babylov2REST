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
            'totalThisMonth' => 0,
            'playedLastMonth' => 0,
            'wonLastMonth' => 0,
            'lostLastMonth' => 0,
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
        }

        return $data;
    }

    public static function makeDataForOneUser($user)
    {
        $ret = self::$stdData;

        $ret['id'] = $user['id'];
        $ret['username'] = $user['username'];
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
    }

    public static function setTotals(&$gameData)
    {
        $total = Game::getTotalGames();

        $gameData['total'] = array_sum($total);
        $gameData['totalThisMonth'] = isset($total[date('Y.m')]) ? $total[date('Y.m')] : 0;
        $gameData['totalLastMonth'] = isset($total[date('Y.m', strtotime('last month'))]) ? $total[date('Y.m', strtotime('last month'))] : 0;
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

    public static function isUserConnected()
    {
        return isset($_SESSION['currentUser']) ? $_SESSION['currentUser'] : null;
    }

    public static function doLoginAction($login, $password)
    {
        //$login = \PDO::quote($login);
        //$password = \PDO::quote($password);
        $user = User::getUniqueUser(self::getFullList("WHERE username = '$login' AND password = '$password'"));

        if ($user !== null) {
            $_SESSION['currentUser'] = $user;
        } else {
            $user = array('message' => 'Mauvais login/mot de passe.');
        }

        return $user;
    }

    public static function doLogoutAction()
    {
        $_SESSION['currentUser'] = null;

        return null;
    }

    public static function getUserDetail($user_id)
    {
        $data = array(
            'gravatar' => "http://www.gravatar.com/avatar/22c64f33e43b433721446315a683ee5a?s=150&d=mm&r=x",
            'userDetail' => array(
                array('text' => "Nombre de parties jouées", 'value' => 3),
                array('text' => "Nombre de parties gagnées", 'value' => 3),
                array('text' => "Nombre de parties perdues", 'value' => 3),
                array('text' => "Score", 'value' => 3),
                array('text' => "Ratio", 'value' => 3),
                array('text' => "Nombre de buts marqués", 'value' => 3),
                array('text' => "Nombre de buts pris", 'value' => 3),
                array('text' => "Nombre moyen de buts marqués", 'value' => 3),
                array('text' => "Nombre moyen de buts pris", 'value' => 3),
                array('text' => "Pire ennemi", 'value' => 3),
                array('text' => "Moins bon adversaire", 'value' => 3),
                array('text' => "Meilleur partenaire", 'value' => 3),
                array('text' => "Moins bon partenaire", 'value' => 3)
            )
        );
        return $data;
    }
}
