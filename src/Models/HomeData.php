<?php

namespace Models;

class HomeData
{
    public static $datas = array('worst', 'fanny');

    public static function get()
    {
        $data = array('gameGraph' => self::getGraphData(), 'alertBar' => array());

        foreach (self::$datas as $func) {
            $data['alertBar'][$func] = self::$func();
        }

        return $data;
    }

    public static function getGraphData()
    {
        $graphData = array();

        foreach (Game::getTotalGames("date") as $date => $nb) {
            $graphData[] = array(strtotime($date) * 1000, intval($nb));
        }

        return $graphData;
    }

    public static function worst()
    {
        return array('value' => 'Joon', 'desc' => '9.46 buts pris par matchs');
    }

    public static function fanny()
    {
        return array('value' => 'Adel. & Nico.', 'desc' => 'Ils ont pris fanny');
    }
}
