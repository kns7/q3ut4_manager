<?php

namespace App\Controller;

class AuthController extends Controller
{
    /**
     * Test if a user is authenticated
     * @return bool
     */
    public function isauth()
    {
        if(isset($_SESSION['authOK'])){
            return ($_SESSION['authOK'] == 'userlogged')?true:false;
        }else{
            return false;
        }
    }

    /**
     * Authenticate User with Password (stored in DB)
     * @param $password
     * @return bool
     */
    public function auth($password)
    {
        if($password == $this->config->wwwpassword){
            $_SESSION['authOK'] = 'userlogged';
            $_SESSION['darkmode'] = 1;
            return true;
        }else{
            unset($_SESSION['authOK']);
            return false;
        }
    }

    /**
     * Logout actual User (destroy Session)
     */
    public function logout()
    {
        $_SESSION['authOK'] = null;
        session_destroy();
    }
}