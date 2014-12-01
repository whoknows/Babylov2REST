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

        foreach (Game::getAllGames($filter) as $userGame) {
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
}
