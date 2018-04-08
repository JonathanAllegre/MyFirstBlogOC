<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 08/04/2018
 * Time: 18:00
 */

namespace App\Manager;

use \PDO;
use App\Entity\PictureEntity;

class PictureManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param PictureEntity $picture
     * @return bool
     */
    public function create(PictureEntity $picture):bool
    {
        $request = $this->pdo->prepare(
            '	INSERT INTO picture (created, name)
						VALUES(:created, :name)'
        );

        $request->bindValue(':created', $picture->getCreated());
        $request->bindValue(':name', $picture->getName());

        if ($request->execute()) {
            return true;
        }

        return false;
    }
}
