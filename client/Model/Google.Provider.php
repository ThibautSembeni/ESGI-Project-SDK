<?php

namespace App\Model;

class GoogleProvider extends Provider {

    private $currentProvider;

    public function __construct()
    {
        $oauth = Oauth::getInstance();
        $this->currentProvider = $oauth->getProviderByName('google'); 
    }

    public function callback()
    {
        try {
            $specifParams = [
                "grant_type" => "authorization_code",
                "code" => $_GET["code"],
            ];
            $data = http_build_query(array_merge([
                "redirect_uri" => $this->currentProvider->getRedirectUri(),
                "client_id" => $this->currentProvider->getClientId(),
                "client_secret" => $this->currentProvider->getClientSecret()
            ], $specifParams));

            $url = $this->currentProvider->getAccessTokenUrl() . "?{$data}";
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $data
                )
            );
            $context = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);

            if (!$result) {
                throw new \InvalidArgumentException(404);
            }

            $result = json_decode($result, true);
            $accessToken = $result['access_token'];
            
            $url = $this->currentProvider->getUserInfoUrl();
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer ' . $accessToken
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $result = json_decode($result, true);
            $oauth = Oauth::getInstance();
            $oauth->setUser($result['name']);
            $oauth->setToken($accessToken);


            header("Location: /oauth-success");

        } catch (\Exception $e) {
            header("Location: {$this->currentProvider->getAuthUrl()}");
        }
    }
}