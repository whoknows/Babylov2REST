<?php

class User extends Phalcon\Mvc\Model
{

    public static $stdData = array(
        //'enabled' => 0,
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

    public static function getFullList($filter = null)
    {
        $data = array();

        foreach (User::find('enabled= 1') as $user) {
            $data[$user->id] = self::makeDataForOneUser($user);
        }

        return $data;
    }

    public static function makeDataForOneUser($user)
    {
        $data = self::$stdData;

        $data['username'] = $user->username;
        //$data['enabled'] = $user->enabled;
        foreach ($user->roles as $role) {
            $data['roles'][] = $role->name;
        }

        $data['gameData'] = self::getGameData($user);

        return $data;
    }

    public static function getGameData($user)
    {
        $data = self::$stdData['gameData'];

        foreach ($user->games as $game) {
            $data['played']++;
            echo json_encode($game) . PHP_EOL;exit;
        }
    }

    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "UsersGames",
            "user_id",
            "game_id",
            "Game",
            "id",
            array('alias' => 'games')
        );

        $this->hasManyToMany(
            "id",
            "UsersRoles",
            "user_id",
            "role_id",
            "Roles",
            "id",
            array('alias' => 'roles')
        );

        $this->hasManyToMany(
            "id",
            "UsersSlots",
            "user_id",
            "slot_id",
            "Slot",
            "id",
            array('alias' => 'slots')
        );
    }
}
