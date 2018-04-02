<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 31/03/2018
 * Time: 09:45
 */

namespace App\Manager;

use App\Entity\RoleEntity;
use \PDO as PDO;

class RoleManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function create(RoleEntity $role)
    {
        $request = $this->pdo->prepare('	INSERT INTO role (title)
									VALUES(:title)');


        $request->bindValue(':id_role', $role->getIdRole());
        $request->bindValue(':title', $role->getTitle());

        $request->execute();
    }

    public function getRole($idRole)
    {
        $request = $this->pdo->prepare('	SELECT *
									FROM role
									WHERE id_role = :id');

        $request->bindValue(':id', $idRole);
        $request->execute();

        $donnees = $request->fetch(PDO::FETCH_ASSOC);

        if (!empty($donnees)) {
            $data = new RoleEntity($donnees);
            return $data;
        }
    }
}
