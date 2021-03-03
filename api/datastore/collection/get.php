<?php 
    require "../../../startenv.php";
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/Collection.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

    // Creating a Collection Object
    $collection = new Collection($db);
    
    $result = $collection->get_collections();
    print_r($result);
?>