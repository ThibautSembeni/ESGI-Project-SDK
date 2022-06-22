<?php

namespace App\Model;

class Oauth 
{

    private $providers;

    public function __construct()
    {
        $providersArray = [];
        $providers_json = file_get_contents('providers.json');
 
        $providers_decoded = json_decode($providers_json, false);

        foreach ($providers_decoded->providers as $key => $provider) {
            array_push($providersArray, new Provider($key, $provider));
        }

        $this->providers = $providersArray;  
    }

    public function getProviders()
    {
        return $this->providers;
    }

}