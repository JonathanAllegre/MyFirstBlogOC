<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 08/04/2018
 * Time: 17:57
 */

namespace App\Entity;

class PictureEntity
{
    private $idImage;
    private $created;
    private $name;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
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
    public function getIdImage()
    {
        return $this->idImage;
    }

    /**
     * @param mixed $idImage
     */
    public function setIdImage($idImage): void
    {
        $this->idImage = $idImage;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
}
