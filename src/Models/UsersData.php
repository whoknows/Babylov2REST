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
