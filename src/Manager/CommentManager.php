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
use PhpParser\Node\Expr\Array_;

class CommentManager extends AppManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param CommentEntity $comment
     * @return bool
     */
    public function create(CommentEntity $comment):bool
    {
        $request = $this->pdo->prepare(
            '	INSERT INTO comment (created, modified, content, id_post, id_comment_statut, id_user)
					    VALUES(:created, :modified, :content, :id_post, :id_comment_statut, :id_user)'
        );


        $request->bindValue(':created', $comment->getCreated());
        $request->bindValue(':modified', $comment->getModified());
        $request->bindValue(':content', $comment->getContent());
        $request->bindValue(':id_post', $comment->getIdPost());
        $request->bindValue(':id_comment_statut', $comment->getIdCommentStatut());
        $request->bindValue(':id_user', $comment->getIdUser());

        if ($request->execute()) {
            return true;
        }
        return false;
    }


    /**
     * @param $postId
     * @return array
     */
    public function getCommentsForPost($postId):array
    {
        $request = $this->pdo->prepare(
            '	SELECT
	                      c.id_comment,
	                      c.created,
	                      c.modified,
	                      c.content,
	                      c.id_post,
	                      c.id_comment_statut,
	                      c.id_user,
	                      u.last_name,
	                      u.first_name
                        FROM comment c
                        INNER JOIN user u ON c.id_user = u.id_user
						WHERE id_post = :id
						ORDER BY c.id_comment DESC'
        );

        $request->bindValue(':id', $postId, PDO::PARAM_INT);
        $request->execute();

        while ($donnees = $request->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new CommentEntity($donnees);
        }

        if (empty($comments)) {
            return null;
        }

        return $comments;
    }

    public function getCommentInStatut($idStatut)
    {
        $request = $this->pdo->prepare(
            '	SELECT
	                      c.id_comment,
	                      c.created,
	                      c.modified,
	                      c.content,
	                      c.id_post,
	                      c.id_comment_statut,
	                      c.id_user,
	                      u.last_name,
	                      u.first_name
                        FROM comment c
                        INNER JOIN user u ON c.id_user = u.id_user
						WHERE c.id_comment_statut = :id
						ORDER BY c.id_comment DESC'
        );

        $request->bindValue(':id', $idStatut, PDO::PARAM_INT);
        $request->execute();

        while ($donnees = $request->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new CommentEntity($donnees);
        }

        if (empty($comments)) {
            return null;
        }

        return $comments;
    }

    public function getAllComments()
    {
        $request = $this->pdo->prepare(
            '	SELECT
	                      c.id_comment,
	                      c.created,
	                      c.modified,
	                      c.content,
	                      c.id_post,
	                      c.id_comment_statut,
	                      c.id_user,
	                      u.last_name,
	                      u.first_name
                        FROM comment c
                        INNER JOIN user u ON c.id_user = u.id_user
						ORDER BY c.id_comment DESC'
        );

        $request->execute();

        while ($donnees = $request->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new CommentEntity($donnees);
        }

        if (empty($comments)) {
            return null;
        }

        return $comments;
    }

    public function read($idComment)
    {
        $request = $this->pdo->prepare(
            'SELECT *
                        FROM comment
                        WHERE id_comment =:id
            '
        );

        $request->bindValue(':id', $idComment, PDO::PARAM_INT);
        $request->execute();

        $data = $request->fetch(PDO::FETCH_ASSOC);

        if (empty($data)) {
            return null;
        }

        $comment = new CommentEntity($data);
        // SET USER
        $user = $this->getUserManager()->getUserById($data['id_user']);
        $comment->setUser($user);

        return $comment;
    }
}
