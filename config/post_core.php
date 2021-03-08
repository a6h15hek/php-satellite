<?php
    require "../../../startenv.php";
    // required headers
    if(isset($_SERVER["HTTP_ORIGIN"])){
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    }else{
        header("Access-Control-Allow-Origin: *");
    }
    header("Content-Type: application/json; charset=UTF-8");
    if($_SERVER["REQUEST_METHOD"] == "OPTIONS"){
        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        exit(0);
    }
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    

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