<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 29/03/2018
 * Time: 14:41
 */

namespace App\controller\frontend;

use App\controller\AppController;

class UserController extends AppController
{
    public function myAccount()
    {

        //TODO: FAIRE LA PAGE MY ACCOUNT UNE FOIS LUSER CONNECTE
        $pass = "Bonjour";
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        if (password_verify('Bnjour', $hash)) {
            echo "lkjhkjh";
        }
    }
}
