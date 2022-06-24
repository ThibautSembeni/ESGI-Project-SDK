<?php

namespace App;

include('helpers/helpers.php');
include("Model/functions.php");

function myAutoloader($class)
{
    //var_dump($class);
    // $class -> "Core\Security" "Model\User
    $class = str_ireplace("App\\", "", $class);
    // $class -> "Core/Security" "Model/User
    $class = str_replace("\\", "/", $class);
    // $class -> "Core/Security"
    if (file_exists($class . ".class.php")) {
        include $class . ".class.php";
    } elseif (file_exists($class . ".php")) {
        include $class . ".php";
    }
}

spl_autoload_register("App\myAutoloader");

$oauth = Model\Oauth::getInstance();

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



$provider = ucfirst($routes[$uri]["provider"]);
$action = strtolower($routes[$uri]["action"]);
$file = 'Model/'.ucfirst($routes[$uri]["provider"]).'.Provider.php';
if (!file_exists($file)) {
    die("La fichier ou la classe n'existe pas");
}

require $file;

$provider = "App\\Model\\" . $provider . "Provider";
if (!class_exists($provider)) {
    die("La classe n'existe pas");
}

$objectProvider = new $provider();

if (!method_exists($objectProvider, $action)) {
    die("La methode n'existe pas");
}

$objectProvider->$action();

