<?php

namespace App\Model;

class Oauth 
{

    private $providers;
    private static $instance = null;
    private $provider_file = null;

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
            if (!file_exists("Model/" . ucfirst($key) . ".Provider.php")) {
                $this->provider_file = fopen("Model/" . ucfirst($key) . ".Provider.php", "w");
                $template_file = 
"<?php\n
namespace App\Model;\n
class " . ucfirst($key) . "Provider extends Provider {\n
    private \$currentProvider;\n
    public function __construct()
    {
        \$oauth = Oauth::getInstance();
        \$this->currentProvider = \$oauth->getProviderByName('$key'); 
    }\n
    public function callback()
    {
        // your code here...
    }
}";
                fwrite($this->provider_file, $template_file);
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