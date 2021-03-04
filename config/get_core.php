<?php 
    require "../../../startenv.php";
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    //imports
    include_once '../../../config/Database.php';
    
    // Initialize database 
    $database = new Database();
    $db = $database->connect();  
?>