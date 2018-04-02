<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 29/03/2018
 * Time: 17:15
 */

namespace App\Entity;

class UserEntity
{
    private $idUser;
    private $lastName;
    private $firstName;
    private $registrationDate;
    private $mailAdress;
    private $password;
    private $idRole;


    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            ### transformation camelCase ####
            $spacing = trim(str_replace("_", " ", $key));
            $spacing = ucwords($spacing);
            $spacing = str_replace(" ", "", $spacing);
            $key = lcfirst($spacing);

            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } //method_exists( $this, $method )
        } //$donnees as $key => $value
    }

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param mixed $idUser
     */
    public function setIdUser($idUser): void
    {
        $this->idUser = $idUser;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @param mixed $registrationDate
     */
    public function setRegistrationDate($registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @return mixed
     */
    public function getMailAdress()
    {
        return $this->mailAdress;
    }

    /**
     * @param mixed $mailAdress
     */
    public function setMailAdress($mailAdress): void
    {
        $this->mailAdress = $mailAdress;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getIdRole()
    {
        return $this->idRole;
    }

    /**
     * @param mixed $idRole
     */
    public function setIdRole($idRole): void
    {
        $this->idRole = $idRole;
    }
}
