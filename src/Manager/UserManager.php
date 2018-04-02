<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 29/03/2018
 * Time: 17:21
 */

namespace App\Manager;

use App\Entity\UserEntity;
use \PDO as PDO;

class UserManager
{
    private $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(UserEntity $user)
    {
        $request = $this->pdo->prepare(
            'INSERT INTO user (
                                          last_name, 
                                          first_name, 
                                          registration_date, 
                                          mail_adress, 
                                          password, 
                                          id_role)
					   VALUES(
					          :last_name,
							  :first_name, 
							  :registration_date, 
							  :mail_adress, 
							  :password, 
							  :id_role)'
        );

        $request->bindValue(':last_name', $user->getLastName());
        $request->bindValue(':first_name', $user->getFirstName());
        $request->bindValue(':registration_date', $user->getRegistrationDate());
        $request->bindValue(':mail_adress', $user->getMailAdress());
        $request->bindValue(':password', $user->getPassword());
        $request->bindValue(':id_role', $user->getIdRole());

        if ($request->execute()) {
            $error = 0;
            $errorTitle = "";
        } else {
            $error = 1;
            $errorTitle = "Un probleme est survenue lors de l'enregistrmenet";
        }

        return array("error" => $error, "errorTitle" => $errorTitle);
    }


    /**
     * @param $mail
     * @return UserEntity
     */
    public function getUserByMail($mail):UserEntity
    {
        $request = $this->pdo->prepare(
            'SELECT id_user,last_name,first_name,mail_adress,u.id_role,r.title as role_title, u.password
                       FROM user u
		               INNER JOIN role r ON u.id_role = r.id_role
		               WHERE mail_adress = :mail'
        );

        $request->bindValue(':mail', $mail);
        $request->execute();

        $donnees = $request->fetch(PDO::FETCH_ASSOC);

        if (!empty($donnees)) {
            $data = new UserEntity($donnees);
            return $data;
        }
    }


    /**
     * @param $idUser
     * @return UserEntity
     */
    public function getUserById($idUser):UserEntity
    {
        $request = $this->pdo->prepare(
            'SELECT id_user,first_name,last_name,registration_date,mail_adress,u.id_role,r.title as role_title
					   FROM user u 
					   INNER JOIN role r ON u.id_role = r.id_role
					   WHERE id_user = :idUser'
        );

        $request->bindValue(':idUser', $idUser);
        $request->execute();

        $donnees = $request->fetch(PDO::FETCH_ASSOC);

        if (!empty($donnees)) {
            $data = new UserEntity($donnees);
            return $data;
        }
    }


    /**
     * @param string $mail
     * @return integer
     */
    public function checkExistMail(string $mail)
    {
        $request = $this->pdo->prepare('SELECT COUNT(id_user) AS nb FROM user WHERE mail_adress = :mail');
        $request->bindValue('mail', $mail);
        $request->execute();

        $donnees = $request->fetch();
        return $donnees['nb'];
    }
}
