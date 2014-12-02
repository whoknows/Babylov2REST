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

    public static function put($slot, $user)
    {
        $sql = "INSERT INTO users_slots (user_id, date, slot_id) SELECT $user as user_id, CURDATE() as date, id FROM slot WHERE name = '$slot'";

        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->exec($sql);

        return $req !== false;
    }

    public static function delete($slot, $user)
    {
        $sql = "DELETE FROM users_slots WHERE date = CURDATE() AND user_id = $user AND slot_id = (SELECT id FROM slot WHERE name = '$slot')";

        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->exec($sql);

        return $req !== false;
    }
}
