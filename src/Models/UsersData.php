<?php

namespace Models;

class UsersData
{
    public static function get()
    {
        $data = array('gameGraph' => self::getGraphData(), 'alertBar' => array());

        foreach (self::$datas as $func) {
            $data['alertBar'][$func] = self::$func();
        }

        return $data;
    }

    public static function getUserDetail($user_id)
    {
        $data = array(
            'gravatar' => "http://www.gravatar.com/avatar/22c64f33e43b433721446315a683ee5a?s=150&d=mm&r=x",
            'userDetail' => array(
                'nbTaken' 3,
                'nbGiven' 3,
                'nbTakenAvg' 3,
                'nbGivenAvg' 3,
                'nemesis' => 3,
                'worstEnemy' => 3,
                'bestMate' => 3,
                'worstMate' => 3
            )
        );
        return $data;
    }
}
