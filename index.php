<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    
    require "startenv.php";
    require_once realpath(__DIR__ . "/vendor/autoload.php");
    include_once "./config/headers.php";
    //imports
    use Dotenv\Dotenv;

    //loading environmental variables
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    print_r(json_encode(
        array(
            "success" => true,
            "message" => "Satellite is at postion."
        )
    ));
?>