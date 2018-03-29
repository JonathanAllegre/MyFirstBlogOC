<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 27/03/2018
 * Time: 17:11
 */

namespace App\controller\frontend;

use App\controller\AppController;
use App\Entity\UserEntity;
use App\Manager\AppManager;
use App\services\AppFactory;
use App\services\FormValidator;
use App\services\LinkBuilder;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ActionsController extends AppController
{
    public function registerUser(
        Flash $flash,
        LinkBuilder $linkBuilder,
        AppFactory $appFactory,
        FormValidator $validator,
        AppManager $manager
    ) {

        $request = $appFactory->getRequest();

        // CHECK ALL FIELDS
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

        // IF NO ERRORS WE CREATE THE ENTITY
        if (!isset($error)) {
            $userManager = $manager->getManager('UserManager');
            $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

            $userEntity = new UserEntity([
                'last_name' => $lastName['data'],
                'first_name' => $firstName['data'],
                'mail_adress' => $email['data'],
                'password' => password_hash($password['data'], PASSWORD_DEFAULT),
                'registration_date' => $date->format('Y-m-d H:i:s'),
                'id_role' => 1
            ]);

            // IF NO ERRORS WE PERSIST DATA
            // IF ERRORS IN PERSIST DATA WE DISPLAY FLASH MESSAGE
            $create = $userManager->create($userEntity);
            if ($create['error']) {
                $error = 1;
                $flash->set('warning', $create['errorTitle']);
            }

            // IF ERRORS WE DISPLAY FORM WITH THE FLASH MESSAGE
            if (isset($error)) {
                $reponse = new Response($this->render('/front/Action/registerUser.html.twig', [
                    'lastName' => $lastName['data'],
                    'firstName' => $firstName['data'],
                    'email' => $email['data'],
                ]));

                $reponse->send();
            }

            // IF NO ERROS AT ALL WE REDIRECT ON USER/MY_ACCOUNT
            echo "GREAT";
            /*
            $response = new RedirectResponse($linkBuilder->getLink('MyAccount'));
            $response->send();
            */
        }


        // IF ERRORS IN VALIDATION FIELD WE DISPLAY FORM WITH THE FLASH MESSAGE
        if (isset($error)) {
            $reponse = new Response($this->render('/front/Action/registerUser.html.twig', [
                'lastName' => $lastName['data'],
                'firstName' => $firstName['data'],
                'email' => $email['data'],
            ]));

            $reponse->send();
        }



    }
}
