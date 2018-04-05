<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 04/04/2018
 * Time: 19:25
 */

namespace App\services;

use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Session\Session;

class CheckPermissions
{
    private $session;
    private $flash;


    public function __construct(Session $session, Flash $flash)
    {
        $this->session = $session;
        $this->flash = $flash;
    }

    public function isAdmin()
    {
        if ($this->session->has('user') && $this->session->get('user')['role_id'] == 2) {
            return true;
        }

        $this->flash->set('warning', "vous n'avez pas access à cette partie du site");
        return false;
    }
}
