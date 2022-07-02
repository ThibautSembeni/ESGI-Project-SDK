<?php

require_once("Controller/functions.php");

$fileRoutes = "routes.yml";

if (file_exists($fileRoutes)) {
    $routes = yaml_parse_file($fileRoutes);
} else {
    die("Le fichier de routing n'existe pas");
}

if (strpos($_SERVER["REQUEST_URI"], '?')) {
    $uri = substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], '?'));
} else {
    $uri = $_SERVER["REQUEST_URI"];
}

if (empty($routes[$uri]) || empty($routes[$uri]["action"])) {
    http_response_code(404);
    die("404 : Not Found");
}

$action = strtolower($routes[$uri]["action"]);
$action();

function home() 
{
    http_response_code(200);
}

function register() 
{
    ['name' => $name, 'url' => $url, 'redirect_uri' => $redirectUri] = $_POST;
    if (findAppBy(['name'=> $name])) {
        http_response_code(409);
        return;
    }
    $app = array_merge(
        ['name' => $name, 'url' => $url, 'redirect_uri' => $redirectUri],
        ['client_id' => uniqid(), 'client_secret' => uniqid()]);
    insertApp($app);
    http_response_code(201);
    echo json_encode($app);
}

function auth() 
{
    ['client_id' => $clientId, 'scope'=> $scope, 'state' => $state, 'redirect_uri' => $redirect_uri] = $_GET;
    $app = findAppBy(['client_id'=> $clientId, 'redirect_uri' => $redirect_uri]);
    if(!$app) {
        http_response_code(404);
        return;
    }
    if (findTokenBy(['client_id' => $clientId])) {
        return authSuccess();
    }
    echo "Name: {$app['name']}<br>";
    echo "Scope: {$scope}<br>";
    echo "URL: {$app['url']}<br>";
    echo "<a href='/auth-success?client_id={$app['client_id']}&state={$state}'>Oui</a>&nbsp;";
    echo "<a href='/failed'>Non</a>";
}

function authSuccess() 
{
    ['client_id' => $clientId, 'state' => $state] = $_GET;
    $app = findAppBy(['client_id'=> $clientId]);
    if(!$app) {
        http_response_code(404);
        return;
    }
    $code = [
        "code" => bin2hex(random_bytes(16)),
        "client_id" => $clientId,
        "expiresAt" => time() + (60*5),
        "user_id" => 1,
    ];
    insertCode($code);
    header("Location: ${app['redirect_uri']}?state=${state}&code=${code['code']}");
}

function handleAuthCode($clientId)
{
    ['code' => $code] = $_GET;
    $code = findCodeBy(['code' => $code, 'client_id'=> $clientId]);
    if(!$code) {
        throw new \InvalidArgumentException(404);
    }
    if($code['expiresAt'] < time()) {
        throw new \InvalidArgumentException(401);
    }
    return $code["user_id"];
}

function handleAuthPassword()
{
    ["username" => $username, "password" => $password] = $_GET;
    $user = findUserBy(['username' => $username, 'password'=> $password]);
    if(!$user) {
        throw new \InvalidArgumentException(404);
    }
    return  $user["id"];
}

function token()
{
    try {
        ['client_id' => $clientId, 'client_secret' => $clientSecret, 'grant_type' => $grantType, 'redirect_uri' => $redirect] = $_GET;
        $app = findAppBy(['client_id'=> $clientId, 'client_secret' => $clientSecret, 'redirect_uri' => $redirect]);
        $user_id = null;
        if(!$app) {
            throw new \InvalidArgumentException(401);
        }
  
        $user_id = match($grantType) {
            'authorization_code' => handleAuthCode($clientId),
            'password' => handleAuthPassword(),
            'client_credentials' => null,
        };
        
        $token = [
            "access_token" => bin2hex(random_bytes(16)),
            "expiresAt" => time() + (60*60*24*30),
            "client_id" => $clientId,
            "user_id"=> $user_id,
        ];
        insertToken($token);
        http_response_code(201);
        echo json_encode([
            "access_token" => $token['access_token'], 
            "expires_in" => $token['expiresAt']
        ]);
        
    } catch (\UnhandledMatchError $th) {
        http_response_code(400);
    } catch (\InvalidArgumentException $e) {
        http_response_code(intval($e->getMessage()));
    }
    

  
}

function me() 
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
    list($type, $token) = explode(' ', $authHeader);
    if($type !== 'Bearer') {
        http_response_code(401);
        return;
    }
    $token = findTokenBy(['access_token' => $token]);
    if(!$token) {
        http_response_code(401);
        return;
    }
    if($token['expiresAt'] < time()) {
        http_response_code(400);
        return;
    }
    // $code = findCodeBy(['code' => $token['code']]);
    // if(!$code) {
    //     http_response_code(401);
    //     return;
    // }
    $user = findUserBy(["id" => $token["user_id"]]);
    echo json_encode([
        "user_id" => $token['user_id'],
        "lastname" => $user["lastname"],
        "firstname" => $user["firstname"],
    ]);
}