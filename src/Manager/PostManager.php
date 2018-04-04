<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 04/04/2018
 * Time: 18:54
 */

namespace App\Manager;

use App\Entity\PostEntity;
use \PDO;

class PostManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(PostEntity $post)
    {
        $request = $this->pdo->prepare(
            '	INSERT INTO post 
	                      (created, modified, title, short_text, content, id_user, id_statut_post, id_image)
						VALUES
						  (:created, :modified, :title, :short_text, :content, :id_user, :id_statut_post, :id_image)'
        );

        $request->bindValue(':created', $post->getCreated());
        $request->bindValue(':modified', $post->getModified());
        $request->bindValue(':title', $post->getTitle());
        $request->bindValue(':short_text', $post->getShortText());
        $request->bindValue(':content', $post->getContent());
        $request->bindValue(':id_user', $post->getIdUser());
        $request->bindValue(':id_statut_post', $post->getIdStatutPost());
        $request->bindValue(':id_image', $post->getIdImage());

        if ($request->execute()) {
            return $error = 0;
        }

        return $error = 1;
    }
}
