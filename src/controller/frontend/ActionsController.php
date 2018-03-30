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
        $validate = $validator->validateRegisterUser($manager, $request, $flash);
        $error = $validate['error'];

        // IF NO ERRORS WE CREATE THE ENTITY
        if (!$error) {
            $userManager = $manager->getManager('UserManager');
            $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));
            $userEntity = new UserEntity([
                'last_name' => $validate['last_name'],
                'first_name' => $validate['first_name'],
                'mail_adress' => $validate['email'],
                'password' => password_hash($validate['password'], PASSWORD_DEFAULT),
                'registration_date' => $date->format('Y-m-d H:i:s'),
                'id_role' => 1
            ]);

            // IF NO ERRORS WE PERSIST DATA
            $create = $userManager->create($userEntity);
            if ($create['error']) {
                $error = 1;
                $flash->set('warning', $create['errorTitle']);
            }

            // IF NO ERROS AT ALL WE REDIRECT ON USER/MY_ACCOUNT
            $response = new RedirectResponse($linkBuilder->getLink('MyAccount'));
            $response->send();

        }

        // IF ERRORS IN VALIDATION FIELD OR IN PERSIST DATA WE DISPLAY FORM WITH THE FLASH MESSAGE
        if ($error) {
            var_dump('dernier');
            $reponse = new Response($this->render('/front/Action/registerUser.html.twig', [
                'lastName' => $validate['last_name'],
                'firstName' => $validate['first_name'],
                'email' => $validate['email'],
            ]));

            $reponse->send();
        }
    }

    public function loginUser(AppFactory $appFactory, AppManager $manager, Flash $flash)
    {
        // GET $POST
        $mail = $appFactory->getRequest()->get('email');
        $pass = $appFactory->getRequest()->get('password');


        // GET USER IN DB
        $userManager = $manager->getManager('UserManager');
        $user = $userManager->login($mail);

        // GET HTTPREFERER
        $ref = $appFactory->getRequest()->server->get('HTTP_REFERER');

        if ($user === null) {
            $error = 1;
            $flash->set('warning', 'Aucune adresse e-mail n\'a été trouvée');
        }

        // CHECK PASSWORD // ADD DATA IN SESSION
        if (!isset($error)) {
            if (password_verify($pass, $user->getPassword())) {
                //TODO: FAIRE LA MIS EN SESSION DES INFO
                $flash->set('success', 'Vous êtes maintenant connecté');
                $response = new RedirectResponse($ref);
                $response->send();
            } else {
                $error = 1;
                $flash->set('warning', 'Le mot de passe saisie ne correspond pas');
            }
        }

        // IF ERRORS
        if (isset($error)) {
            $response = new RedirectResponse($ref);
            $response->send();
        }
    }
}
