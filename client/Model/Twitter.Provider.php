<?php

namespace App\Model;

class TwitterProvider extends Provider {

    private $currentProvider;

    public function __construct()
    {
        $oauth = Oauth::getInstance();
        $this->currentProvider = $oauth->getProviderByName('twitter'); 
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
                "client_secret" => $this->currentProvider->getClientSecret(),
                "code_verifier" => $this->currentProvider->getParams()->code_challenge
            ], $specifParams));

            $url = $this->currentProvider->getAccessTokenUrl();
            $loginsEncoded = base64_encode($this->currentProvider->getClientId() . ":" . $this->currentProvider->getClientSecret());
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    "header" => ["Authorization: Basic " . $loginsEncoded,
                                "Content-Type: application/x-www-form-urlencoded"],
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
            $oauth->setUser($result['data']['username']);
            $oauth->setToken($accessToken);

            header("Location: /oauth-success");

        } catch (\Exception $e) {
            header("Location: {$this->currentProvider->getAuthUrl()}");
        }
    }
}