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
            //array_push($providersArray, new Provider($key, $provider));
            $providersArray[$key] = new Provider($key, $provider);
            
            $redirect_uri = str_replace([$_SERVER["HTTP_HOST"], "http://", "https://"], "", $provider->redirect_uri);
            if (array_key_exists($redirect_uri, $routes) == false) {
                $data .= $redirect_uri . ":\n";
                $data .= "  provider: " . $key . "\n";
                $data .= "  action: callback \n";
                file_put_contents('routes.yml', $data, FILE_APPEND);
            }
        }
        
        $this->providers = $providersArray;  
        session_start();

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

    public function getProviderByName(string $name)
    {
        return $this->providers[$name];
    }

    public function setUser($user)
    {
        $_SESSION['user'] = $user;
    }

    public function getUser()
    {
        return $_SESSION['user'];
    }

    public function setToken($token)
    {
        $_SESSION["AccessToken"] = $token;
    }

    public function getToken()
    {
        return $_SESSION["AccessToken"];
    }

}