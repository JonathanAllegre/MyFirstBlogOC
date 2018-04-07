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
            '	SELECT 
                          ps.title AS \'statut_post_title\',
                          p.id_post,
                          p.created,
                          p.modified,
                          p.title,
                          p.short_text,
                          p.content,
                          p.id_user,
                          p.id_statut_post,
                          p.id_image,
                          u.first_name,
                          u.last_name
					    FROM post p
					    INNER JOIN user u ON p.id_user = u.id_user
					    INNER JOIN post_statut ps ON p.id_statut_post = ps.id_statut_post
						WHERE id_post = :id'
        );

        $request->bindValue(':id', $postId, PDO::PARAM_INT);
        $request->execute();

        $data = $request->fetch(PDO::FETCH_ASSOC);

        if (empty($data)) {
            return null;
        }
        $post = new PostEntity($data);
        return $post;
    }


    public function getAllPost($limit = null)
    {

        $limit = (!is_null($limit)) ? " LIMIT 0,".$limit :null;

        $sql = "
                SELECT 
                  ps.title AS 'statut_post_title',
                  p.id_post,
                  p.created,
                  p.modified,
                  p.title,
                  p.short_text,
                  p.content,
                  p.id_user,
                  p.id_statut_post,
                  p.id_image,
                  u.first_name,
                  u.last_name
                FROM post p
                INNER JOIN user u ON p.id_user = u.id_user
                INNER JOIN post_statut ps ON p.id_statut_post = ps.id_statut_post
                ORDER BY p.id_post DESC";

        $sql = $sql.$limit;

        $request = $this->pdo->prepare($sql);
        $request->execute();

        while ($donnees = $request->fetch(PDO::FETCH_ASSOC)) {
            $data[] = new PostEntity($donnees);
        }
        if (empty($data)) {
            return null;
        }

        return $data;
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
     * @param $idPost
     * @return bool
     */
    public function delete($idPost):bool
    {
        $request = $this->pdo->prepare(
            'DELETE FROM post
					   WHERE id_post = :idPost'
        );
        $request->bindValue(':idPost', $idPost);
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

        if (empty($data)) {
            return null;
        }

        return $data['id_post'];
    }
}
