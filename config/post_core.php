<?php
    require "../../../startenv.php";
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../config/authentication.php';

    //check authentication
    if(!checkAuth()){
        print_r(json_encode(
            array(
                'success'=>false,
                'message' => "empty"
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