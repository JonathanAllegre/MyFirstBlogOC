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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AppController
{

    /**
     * @param AppManager $manager
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function allComments(AppManager $manager)
    {
        $noValidates = $manager->getCommentManager()->getCommentInStatut(1);
        $validates = $manager->getCommentManager()->getCommentInStatut(2);

        $reponse = new Response($this->render('/back/Comment/list.html.twig', [
            'active' => 'comments',
            'validateComments' => $validates,
            'noValidateComments' => $noValidates
        ]));
        return $reponse->send();
    }


    /**
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function validate()
    {

        // DI
        $container = $this->container;

        // GET COMMENT ID
        $commentId = $container->getRequestParameters()->getParameters('id_comment');

        // GET COMMENT
        $comment = $container->getManager()->getCommentManager()->read($commentId);

        // ------------- IF METHOD = POST ( IF FORM POST IS SEND ) ---------
        if ($this->container->getRequest()->server->get('REQUEST_METHOD') == "POST") {
            $formData = $this->container->getRequest()->request->all();

            // CHECK IF TOKENS MATCH
            if ($formData['myToken'] != $this->container->getSession()->get('myToken')) {
                $container->getFlash()->set('warning', 'Erreur de token');
                $response = new RedirectResponse($container->getLinkBuilder()->getLink('Home'));
                return $response->send();
            }

            if (isset($formData['validate'])) {
                // UPDATE COMMENT SET VALIDATE
                $comment->setIdCommentStatut(2);

                // PERSIST
                if ($container->getManager()->getCommentManager()->update($comment)) {
                    $container->getFlash()->set('success', "Le commentaire est maintenant en ligne");
                    $response = new RedirectResponse($container->getLinkBuilder()->getLink('HomeAdmin'));
                    return $response->send();
                }
            }
            if (isset($formData['delete'])) {
                // DELETE COMMENT
                if ($container->getManager()->getCommentManager()->delete($comment->getIdComment())) {
                    $container->getFlash()->set('success', "Le commentaire à été correctement supprimé.");
                    $response = new RedirectResponse($container->getLinkBuilder()->getLink('HomeAdmin'));
                    return $response->send();
                }
            }
        }
        
        $reponse = new Response($this->render('/back/Comment/validate.html.twig', [
            'active' => 'comments',
            'comment' => $comment,
            'myToken' => $this->container->getSession()->get('myToken'),
        ]));
        return $reponse->send();
    }
}
