<?php

namespace App\Model;

class LoginProvider extends Provider {

    public function getView()
    {
        include("view/index.php");
    }
}