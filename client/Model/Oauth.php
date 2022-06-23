<?php

namespace App\Model;

class Oauth 
{

    private $providers;
    private static $instance = null;

    public function __construct()
    {
        $providersArray = [];
        $providers_json = file_get_contents('providers.json');
        $data = "";
 
        $providers_decoded = json_decode($providers_json, false);

        
        foreach ($providers_decoded->providers as $key => $provider) {
            $routes = yaml_parse_file("routes.yml");
            $data = "";
            array_push($providersArray, new Provider($key, $provider));
            if (array_key_exists("/".explode("/", $provider->redirect_uri)[3], $routes) == false) {
                $data .= "/" . explode("/", $provider->redirect_uri)[3] . ":\n";
                $data .= "  provider: " . $key . "\n";
                $data .= "  action: callback \n";
                file_put_contents('routes.yml', $data, FILE_APPEND);
            }
        }
        
        $this->providers = $providersArray;  
    }

    public static function getInstance()
	{
		if (is_null( self::$instance) ) {
			self::$instance = new Oauth();
		}
		return self::$instance;
	}

    public function getProviders()
    {
        return $this->providers;
    }

}