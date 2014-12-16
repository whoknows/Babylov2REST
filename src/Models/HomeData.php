<?php

namespace Models;

class HomeData
{
    public static $datas = array('fanny');

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
        $data = array_values(Game::getAllGames("WHERE score_team1 = 0 OR score_team2 = 0", "LIMIT 0,4"))[0];

        if($data['st1'] == 0){
            return array('users' => array($data['p1t1'], $data['p2t1']), 'date' => $data['date']);
        } else {
            return array('users' => array($data['p1t2'], $data['p2t2']), 'date' => $data['date']);
        }
    }
}
