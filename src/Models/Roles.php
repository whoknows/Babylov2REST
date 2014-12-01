<?php

namespace Models;

class Roles
{
    public static function getRoles($filter = "")
    {
        $sql = "SELECT GROUP_CONCAT(DISTINCT name) as name
                FROM roles
                INNER JOIN users_roles ON roles.id = role_id
                $filter";

        $bdd = \Config\Database::getInstance();
        $req = $bdd->getConnection()->query($sql);

        return $req->fetch(\PDO::FETCH_ASSOC)['name'];
    }
}
