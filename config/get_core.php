<?php 
    require "../../../startenv.php";
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../config/authentication.php';

    //check authentication
    if(!checkAuth()){
        print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Access Denied."
            )
        ));
        exit();
    }

    // Initialize database 
    $database = new Database();
    $db = $database->connect();  

?>