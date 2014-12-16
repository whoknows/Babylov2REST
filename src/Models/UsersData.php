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

}
