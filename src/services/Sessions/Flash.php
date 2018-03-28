<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 27/03/2018
 * Time: 20:37
 */

namespace App\services\Sessions;

use Symfony\Component\HttpFoundation\Session\Session;

class Flash
{
    /**
     * @var Session
     */
    private $session;


    /**
     * Flash constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }


    /**
     * @param string $key
     * @param $message
     */
    public function set(string $key, $message):void
    {
        $this->session->getFlashBag()->add(
            $key,
            $message
        );
    }

    /**
     * @param $key
     * @return array
     */
    public function get($key):array
    {
        $message = $this->session->getFlashBag()->get($key);
        return $message;
    }
}
