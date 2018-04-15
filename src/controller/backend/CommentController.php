<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 14/04/2018
 * Time: 10:12
 */

namespace App\controller\backend;

use App\controller\AppController;
use App\Manager\AppManager;
use App\services\CheckPermissions;
use App\services\LinkBuilder;
use App\services\RequestParameters;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AppController
{
    public function validate(
        AppManager $manager,
        CheckPermissions $checkPermissions,
        Flash $flash,
        LinkBuilder $linkBuilder,
        RequestParameters $requestParameters
    ) {

        // IF USER IS NOT CONNECT OR IF USER DON'T HAVE PERMISION
        if (!$checkPermissions->isAdmin()) {
            $flash->set('warning', "vous n'avez pas access à cette partie du site");
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            return $response->send();
        }

        // GET COMMENT ID
        $commentId = $requestParameters->getParameters('id_comment');

        // GET COMMENT
        $comment = $manager->getCommentManager()->read($commentId);

        // ------------- IF METHOD = POST ( IF FORM POST IS SEND ) ---------
        if ($this->getApp()->getRequest()->server->get('REQUEST_METHOD') == "POST") {
            $formData = $this->getApp()->getRequest()->request->all();

            // CHECK IF TOKENS MATCH
            if ($formData['myToken'] != $this->getSession()->get('myToken')) {
                $flash->set('warning', 'Erreur de token');
                $response = new RedirectResponse($linkBuilder->getLink('Home'));
                return $response->send();
            }

            if (isset($formData['validate'])) {
                // UPDATE COMMENT SET VALIDATE
                $comment->setIdCommentStatut(2);

                // PERSIST
                if ($manager->getCommentManager()->update($comment)) {
                    $flash->set('success', "Le commentaire est maintenant en ligne");
                    $response = new RedirectResponse($linkBuilder->getLink('HomeAdmin'));
                    return $response->send();
                }
            }
            if (isset($formData['delete'])) {
                // DELETE COMMENT
                if ($manager->getCommentManager()->delete($comment->getIdComment())) {
                    $flash->set('success', "Le commentaire à été correctement supprimé.");
                    $response = new RedirectResponse($linkBuilder->getLink('HomeAdmin'));
                    return $response->send();
                }
            }
        }


        $reponse = new Response($this->render('/back/Comment/validate.html.twig', [
            'active' => 'comments',
            'comment' => $comment,
            'myToken' => $this->getSession()->get('myToken'),
        ]));
        return $reponse->send();
    }
}
