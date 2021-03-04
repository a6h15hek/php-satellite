<?php 
    require "startenv.php";
    require_once realpath(__DIR__ . "/vendor/autoload.php");
    //imports
    use Dotenv\Dotenv;

    //loading environmental variables
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
?>