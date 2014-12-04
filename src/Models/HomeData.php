<?php

namespace Models;

class HomeData
{
    public static $datas = array('victory', 'defeat', 'games', 'last', 'worst', 'fanny');

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
        $graphData = array('labels' => array(), 'datas' => array());

        foreach (Game::getTotalGames("date") as $date => $nb) {
            $graphData['labels'][] = date('Y-m-d', strtotime($date));
            $graphData['datas'][] = $nb;
        }

        return $graphData;
    }

    public static function victory()
    {
        return array('value' => 'Guillaume', 'desc' => '8 parties gagnées');
    }

    public static function defeat()
    {
        return array('value' => 'Cédric', 'desc' => '8 parties perdues');
    }

    public static function games()
    {
        return array('value' => '8', 'desc' => '8 parties jouées');
    }

    public static function last()
    {
        return array('value' => 'Stephane', 'desc' => '0.1 de score');
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