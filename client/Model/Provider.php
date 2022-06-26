<?php

namespace App\Model;

class Provider 
{
    private $name;
    private $client_id;
    private $client_secret;
    private $auth_url;
    private $user_info_url;
    private $access_token_url;
    private $redirect_uri;
    private $scope;
    private $params = null;

    public function __construct($name = null, $options = null)
    {
        if (!is_null($options)) {
            $this->name = $name;
            $this->client_id = $options->client_id;
            $this->client_secret = $options->client_secret;
            $this->auth_url = $options->auth_url;
            $this->user_info_url = $options->user_info_url;
            $this->access_token_url = $options->access_token_url;
            $this->redirect_uri = $options->redirect_uri;
            $this->scope = $options->scope;
            if (property_exists( (object) $options, "params")) {
                $this->params = $options->params;
            }
        }   
    }

    public function getName()
    {
        return $this->name;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getClientSecret()
    {
        return $this->client_secret;
    }

    public function getUserInfoUrl()
    {
        return $this->user_info_url;
    }

    public function getAccessTokenUrl()
    {
        return $this->access_token_url;
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getAuthURl()
    {
        
        // $queryParams= http_build_query(array(
        //     "client_id" => $this->client_id,
        //     "redirect_uri" => $this->redirect_uri,
        //     "response_type" => "code",
        //     "scope" => $this->scope,
        //     "state" => bin2hex(random_bytes(16))
        // ));
        if (is_null($this->params)) {
            $queryParams= http_build_query(array(
                "client_id" => $this->client_id,
                "redirect_uri" => $this->redirect_uri,
                "response_type" => "code",
                "scope" => $this->scope,
                "state" => bin2hex(random_bytes(16))
            ));
        } else {
            $queryParams= http_build_query(array_merge(array(
                "client_id" => $this->client_id,
                "redirect_uri" => $this->redirect_uri,
                "response_type" => "code",
                "scope" => $this->scope,
                "state" => bin2hex(random_bytes(16))
            ), (array) $this->params));
        }
        return $this->auth_url."?{$queryParams}";
    }

}