<?php

namespace App\Model;

class OauthSuccessProvider extends Provider {

    public function getView()
    {
        dd(Oauth::getInstance()->getUser());
        include("view/oauth_result.php");
    }
}