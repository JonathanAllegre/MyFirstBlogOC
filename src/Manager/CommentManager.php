<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/04/2018
 * Time: 18:20
 */

namespace App\Manager;

use App\Entity\CommentEntity;
use \PDO;

class CommentManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(CommentEntity $comment)
    {
        return $comment;
    }
}
