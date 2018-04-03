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

/**
 * Class RoleManager
 * @package App\Manager
 */
class RoleManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * @param RoleEntity $role
     */
    public function create(RoleEntity $role):void
    {
        $request = $this->pdo->prepare('	INSERT INTO role (title)
									VALUES(:title)');


        $request->bindValue(':id_role', $role->getIdRole());
        $request->bindValue(':title', $role->getTitle());

        $request->execute();
    }

    /**
     * @param $idRole
     * @return RoleEntity
     */
    public function getRole($idRole):RoleEntity
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

        return null;
    }
}
