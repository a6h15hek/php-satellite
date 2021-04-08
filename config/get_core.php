<?php 
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

    if(!strcmp($auth_data->role, "user") || !strcmp($auth_data->role, "client")){
        if(strcmp($server_name, $_ENV['ALLOW_ORIGIN']) && strcmp($_ENV['ALLOW_ORIGIN'], "*")){
            http_response_code(401);
            print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "Access Denied. set ALLOW_ORIGIN = <your-client-address> in .env file"
                )
            ));
            exit();   
        }
    }


    // Initialize database 
    $database = new Database();
    $db = $database->connect();  

?>
