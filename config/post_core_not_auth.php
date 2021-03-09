<?php
    require "../../../startenv.php";
    include_once "headers.php";

    //imports
    include_once '../../../config/Database.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));
        
?>