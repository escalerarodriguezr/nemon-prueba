<?php
declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

session_start();

define("ROOT_PATH", dirname(__DIR__));
require ROOT_PATH. '/vendor/autoload.php';
require ROOT_PATH. '/config/constants/framework.php';


$dotenv = new Dotenv();
if( file_exists(ROOT_PATH.'/.env') &&
    file_exists(ROOT_PATH.'/.env.local') &&
    file_exists(ROOT_PATH.'/.env.test')
){
    $dotenv->load(ROOT_PATH.'/.env', ROOT_PATH.'/.env.local', ROOT_PATH.'/.env.test');
}elseif ( file_exists(ROOT_PATH.'/.env') &&
    file_exists(ROOT_PATH.'/.env.local')){
    $dotenv->load(ROOT_PATH.'/.env', ROOT_PATH.'/.env.local');
}else{
    $dotenv->load(ROOT_PATH.'/.env');
}

$routes = require CONFIG_PATH. '/routes.php';
$container = require CONFIG_DEPENDENCY_INJECTION_PATH. '/container.php';

$request = Request::createFromGlobals();

try{
    $kernel = $container->get('simplex');
}catch (\Exception $exception){
    $response = new Response('An error occurred', 500);
}

$response = $kernel->handle($request);
$response->prepare($request);
$response->send();




