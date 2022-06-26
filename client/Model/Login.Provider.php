<?php

namespace App\Model;

class LoginProvider extends Provider {

    public function __construct()
    {
        session_unset();
    }

    public function getView()
    {
        include("view/index.php");
    }
}