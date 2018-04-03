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
use Symfony\Component\HttpFoundation\Session\Session;

class ActionsController extends AppController
{
    /**
     * @param Flash $flash
     * @param LinkBuilder $linkBuilder
     * @param AppFactory $appFactory
     * @param FormValidator $validator
     * @param AppManager $manager
     */
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
            $userManager = $manager->getUserManager();
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
            $flash->set(
                'success',
                'Votre compte a bien été créer. Vous pouvez maintenenat vous connecter en utilisant vos identifiants.'
            );
            $response = new RedirectResponse($linkBuilder->getLink('MyAccount'));
            $response->send();
        }

        // IF ERRORS IN VALIDATION FIELD OR IN PERSIST DATA WE DISPLAY FORM WITH THE FLASH MESSAGE
        if ($error) {
            $reponse = new Response($this->render('/front/Action/registerUser.html.twig', [
                'lastName' => $validate['last_name'],
                'firstName' => $validate['first_name'],
                'email' => $validate['email'],
            ]));

            $reponse->send();
        }
    }

    /**
     * @param AppFactory $app
     * @param Session $session
     * @param FormValidator $validator
     * @param Flash $flash
     * @param LinkBuilder $linkBuilder
     * @param AppManager $manager
     * @return RedirectResponse
     */
    public function deleteUser(
        AppFactory $app,
        Session $session,
        FormValidator $validator,
        Flash $flash,
        LinkBuilder $linkBuilder,
        AppManager $manager
    ) {

        // VALIDATE FORM
        $request = $app->getRequest();
        $validate = $validator->validateDeleteUser($request, $session, $flash);

        // URL DE REDIRECTION
        $homeUrl = $linkBuilder->getLink('Home');

        // IF ERRORS REDIRECT TO HOME
        if ($validate) {
            $response = new RedirectResponse($homeUrl);
            return $response->send();
        }


        // GET USER ID IN SESSION
        $user = $session->get('user');

        // DELETE USER IN BDD IF ERROR WE REDIRECT TO HOME
        if ($manager->getUserManager()->deleteUser($user['id'])) {
            // FLASH MESSAGE WARNING
            $flash->set('warning', 'Une erreur est survenue lors de la suppression de votre compte');


            // REDIRECT TO HOME
            $response = new RedirectResponse($homeUrl);
            return $response->send();
        }

        // REMOVE SESSION USER & TOKEN
        $session->remove('user');
        $session->remove('myToken');

        // FLASH MESSAGE SUCCESS
        $flash->set('success', 'Votre compte à bien été supprimé');

        // REDIRECT TO HOME
        $response = new RedirectResponse($homeUrl);
        return $response->send();
    }

    /**
     * @param AppFactory $appFactory
     * @param AppManager $manager
     * @param Flash $flash
     * @param Session $session
     * @throws \Exception
     */
    public function loginUser(AppFactory $appFactory, AppManager $manager, Flash $flash, Session $session)
    {

        // GET $POST
        $mail = $appFactory->getRequest()->get('email');
        $pass = $appFactory->getRequest()->get('password');

        // GET USER IN DB
        $userManager = $manager->getUserManager();
        $user = $userManager->getUserByMail($mail);

        // GET HTTPREFERER
        $ref = $appFactory->getRequest()->server->get('HTTP_REFERER');

        if ($user === null) {
            $error = 1;
            $flash->set('warning', 'Aucune adresse e-mail n\'a été trouvée');
        }

        // CHECK PASSWORD // ADD DATA IN SESSION
        if (!isset($error)) {
            if (password_verify($pass, $user->getPassword())) {
                // MAKE ARRAY INFO USER
                $infoUser = array(
                    'id' => $user->getIdUser(),
                    'email' => $user->getMailAdress(),
                    'first_name' => $user->getFirstName(),
                    'last_name' => $user->getLastName(),
                    'role_id' => $user->getIdRole(),
                    'role_title' => $user->getRoleTitle(),
                );

                // ADD USER DATA IN SESSION && TOKEN
                $session->set('user', $infoUser);
                $session->set('myToken', bin2hex(random_bytes(32)));

                // IF NO ERROR WE REDIRECT
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

    /**
     * @param Session $session
     * @param AppFactory $appFactory
     * @param Flash $flash
     */
    public function logoutUser(Session $session, AppFactory $appFactory, Flash $flash)
    {

        // GET HTTPREFERER
        $ref = $appFactory->getRequest()->server->get('HTTP_REFERER');

        // REMOVE SESSION USER & TOKEN
        $session->remove('user');
        $session->remove('myToken');

        // REDIRECT
        $flash->set('success', 'Vous êtes maintenant déconnecté');
        $response = new RedirectResponse($ref);
        $response->send();
    }
}
