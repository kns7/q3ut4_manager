<?php

namespace App\Controller;

class AuthController extends Controller
{
    public function isauth()
    {
        if(isset($_SESSION['authOK'])){
            return ($_SESSION['authOK'] == 'userlogged')?true:false;
        }else{
            return false;
        }
    }

    public function auth($password)
    {
        if($this->app->config->www_password == $password){
            $_SESSION['authOK'] = 'userlogged';
            return true;
        }else{
            unset($_SESSION['authOK']);
            return false;
        }
    }

    public function logout()
    {
        $_SESSION['authOK'] = null;
        session_destroy();
    }
}