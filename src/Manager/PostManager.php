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

    /**
     * @param PostEntity $post
     * @return boolean
     */
    public function create(PostEntity $post):bool
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
            return true;
        }

        return false;
    }

    /**
     * @param $postId
     * @return PostEntity
     */
    public function read($postId)
    {
        $request = $this->pdo->prepare(
            '	SELECT *
					    FROM post p
					    INNER JOIN user u ON p.id_user = u.id_user
						WHERE id_post = :id'
        );

        $request->bindValue(':id', $postId, PDO::PARAM_INT);
        $request->execute();

        $data = $request->fetch(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $post = new PostEntity($data);
            return $post;
        }

        return false;
    }


    /**
     * @param PostEntity $post
     * @return bool
     */
    public function update(PostEntity $post):bool
    {
        $request = $this->pdo->prepare(
            '	UPDATE post
                        SET
						   created = :created,
						   modified = :modified,
						   title = :title,
						   short_text = :short_text,
						   content = :content,
						   id_user = :id_user,
						   id_statut_post = :id_statut_post,
						   id_image = :id_image                        		
                        WHERE id_post = :id_post '
        );

        $request->bindValue(':id_post', $post->getIdPost());
        $request->bindValue(':created', $post->getCreated());
        $request->bindValue(':modified', $post->getModified());
        $request->bindValue(':title', $post->getTitle());
        $request->bindValue(':short_text', $post->getShortText());
        $request->bindValue(':content', $post->getContent());
        $request->bindValue(':id_user', $post->getIdUser());
        $request->bindValue(':id_statut_post', $post->getIdStatutPost());
        $request->bindValue(':id_image', $post->getIdImage());

        if ($request->execute()) {
            return true;
        }
        
        return false;
    }

    /**
     * @return int
     */
    public function getLastId():int
    {
        $request = $this->pdo->query(
            'SELECT id_post
                       FROM post
                       ORDER BY id_post DESC 
                       LIMIT 0,1'
        );

        $request->execute();
        $data = $request->fetch(PDO::FETCH_ASSOC);

        var_dump($data);

        if (!empty($data)) {
            return $data['id_post'];
        }

        return null;
    }
}
