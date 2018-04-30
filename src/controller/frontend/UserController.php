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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AppController
{

    /**
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function registerUser()
    {

        // DEPENDENCY
        $flash = $this->container->getFlash();
        $linkBuilder = $this->container->getLinkBuilder();
        $request = $this->container->getRequest();
        $validator = $this->container->getFormValidator();
        $manager = $this->container->getManager();

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

            // WE PERSIST DATA
            $create = $userManager->create($userEntity);
            if ($create['error']) {
                $error = 1;
                $flash->set('warning', $create['errorTitle']);
            }


            // IF NO ERROS AT ALL WE REDIRECT ON USER/MY_ACCOUNT
            if (!$error) {
                $flash->set(
                    'success',
                    'Votre compte a bien été créer. Vous pouvez vous connecter en utilisant vos identifiants'
                );
                $response = new RedirectResponse($linkBuilder->getLink('Home'));
                return $response->send();
            }
        }

        // IF ERRORS IN VALIDATION FIELD OR IN PERSIST DATA WE DISPLAY FORM WITH THE FLASH MESSAGE
        $reponse = new Response($this->render('/front/User/registerUser.html.twig', [
            'lastName' => $validate['last_name'],
            'firstName' => $validate['first_name'],
            'email' => $validate['email'],
        ]));

        return $reponse->send();
    }


    /**
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function deleteUser()
    {

        //DEPENDENCY
        $session = $this->container->getSession();
        $validator = $this->container->getFormValidator();
        $flash = $this->container->getFlash();
        $linkBuilder = $this->container->getLinkBuilder();
        $manager = $this->container->getManager();


        // VALIDATE FORM
        $request = $this->container->getRequest();
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
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function loginUser()
    {

        //DEPENDENCY
        $session = $this->container->getSession();
        $flash = $this->container->getFlash();
        $manager = $this->container->getManager();

        // GET $POST
        $mail = $this->container->getRequest()->get('email');
        $pass = $this->container->getRequest()->get('password');

        // GET USER IN DB
        $userManager = $manager->getUserManager();
        $user = $userManager->getUserByMail($mail);

        // GET HTTPREFERER
        $ref = $this->container->getRequest()->server->get('HTTP_REFERER');

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
                return $response->send();
            }

            $flash->set('warning', 'Le mot de passe saisie ne correspond pas');
        }

        // IF ERRORS
        $response = new RedirectResponse($ref);
        return $response->send();
    }


    /**
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function logoutUser()
    {

        //DEPENDENCY
        $session = $this->container->getSession();
        $flash = $this->container->getFlash();

        // GET HTTPREFERER
        $ref = $this->container->getRequest()->server->get('HTTP_REFERER');

        // REMOVE SESSION USER & TOKEN
        $session->remove('user');
        $session->remove('myToken');

        // REDIRECT
        $flash->set('success', 'Vous êtes maintenant déconnecté');
        $response = new RedirectResponse($ref);
        return $response->send();
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function myAccount()
    {

        //DEPENDENCY
        $session = $this->container->getSession();
        $manager = $this->container->getManager();
        $linkBuilder = $this->container->getLinkBuilder();

        // IF $SESSION.USER DON'T EXIST WE REDIRECT TO HOME
        if (!$session->get('user')) {
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            $response->send();
        }

        // GET USER SESSION & TOKEN
        $userSession = $session->get('user');
        $token = $session->get('myToken');

        // GET USER OBJECT
        $user = $manager->getUserManager()->getUserById($userSession['id']);


        // SET RESPONSE
        $reponse = new Response($this->render('/front/User/myAccount.html.twig', [
            'user' => $user,
            'token' => $token,
        ]));
        $reponse->send();
    }
}
