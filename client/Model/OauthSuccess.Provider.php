<?php

namespace App\Model;


class OauthSuccessProvider extends Provider {

    public function getView()
    {
        include("view/oauth_result.php");
    }
}