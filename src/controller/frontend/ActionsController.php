<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 27/03/2018
 * Time: 17:11
 */

namespace App\controller\frontend;

use App\controller\AppController;
use App\services\AppFactory;
use App\services\FormValidator;
use App\services\LinkBuilder;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionsController extends AppController
{
    public function registerUser(
        Flash $flash,
        LinkBuilder $linkBuilder,
        AppFactory $appFactory,
        FormValidator $validator
    ) {
        $request = $appFactory->getRequest();

        $lastName = $validator->sanitizeString($request->request->get('last_name'), 'nom', true);
        if ($lastName['error']) {
            $error = 1;
            $flash->set('warning', $lastName['errorTitle']);
        }

        $firstName = $validator->sanitizeString($request->request->get('first_name'), 'Prénom', true);
        if ($firstName['error']) {
            $error = 1;
            $flash->set('warning', $firstName['errorTitle']);
        }

        $email = $validator->validateEmailField($request->request->get('email'), true);
        if ($email['error']) {
            $error = 1;
            $flash->set('warning', $email['errorTitle']);
        }

        $password = $validator->sanitizeString($request->request->get('password'), "Mot de passe", true);
        if ($password['error']) {
            $error = 1;
            $flash->set('warning', $password['errorTitle']);
        }


        if (isset($error)) {
            $reponse = new Response($this->render('/front/Action/registerUser.html.twig', [
                'lastName' => $lastName['data'],
                'firstName' => $firstName['data'],
                'email' => $email['data'],
            ]));

            $reponse->send();
        }


        if (!isset($error)) {
            $response = new RedirectResponse($linkBuilder->getLink('MyAccount'));
            $response->send();
        }


        /// SI NOT OK




        //// SI OK ON REDIRIGE VERS user/account/view
    }
}
