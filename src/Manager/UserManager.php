<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 29/03/2018
 * Time: 17:21
 */

namespace App\Manager;

use App\Entity\UserEntity;

class UserManager
{
    private $pdo;


    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(UserEntity $user)
    {
        $request = $this->pdo->prepare('INSERT INTO user (
                                                  last_name, 
                                                  first_name, 
                                                  registration_date, 
                                                  mail_adress, 
                                                  password, 
                                                  id_role)
								 VALUES(:last_name,
								  :first_name, 
								  :registration_date, 
								  :mail_adress, 
								  :password, 
								  :id_role)');


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
}
