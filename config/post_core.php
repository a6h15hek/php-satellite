<?php
    require "../../../startenv.php";
    include_once "headers.php";
    

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../config/authentication.php';

    //check authentication
    $auth_data = checkAuth();
    if(!$auth_data){
        http_response_code(401);
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

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));
        
?>