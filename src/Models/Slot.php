<?php

namespace Models;

class Slot
{

    public static function getAllSlots($filter = "")
    {
        $sql = "SELECT name, user_id, b.id
                FROM slot a
                LEFT JOIN `users_slots` b ON a.id = b.`slot_id` AND date = CURDATE()
                $filter
                ORDER BY a.name";

        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->query($sql);

        return self::formatData($req->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function formatData($data)
    {
        $ret = array();

        foreach ($data as $slot) {
            if (!isset($ret[$slot['name']])) {
                $ret[$slot['name']]['creneau'] = $slot['name'];
                $ret[$slot['name']]['users'] = array();
            }

            if($slot['user_id'] != null) {
                $ret[$slot['name']]['users'][] = $slot['user_id'];
            }
        }

        return $ret;
    }
}
