<?php
    require "../../../startenv.php";
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // database connection will be here

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/User.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

    // instantiate product object
    $user = new User($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));
        
    if($data === "" || $data === null){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "collections name not defined or dataobject not defined"
            )
        ));
    }

    // set product property values
    $user->firstname = $data->firstname;
    $user->lastname = $data->lastname;
    $user->email = $data->email;
    $user->password = $data->password;

    // create the user
    if(
        !empty($user->firstname) &&
        !empty($user->email) &&
        !empty($user->password)
    ){
        if($user->emailExists()){
            return print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "User with this email already exists"
                )
            ));
        }
        $result = $user->create();
        print_r($result);
    }
    
    // message if unable to create user
    else{
    
        // set response code
        http_response_code(400);
    
        // display message: unable to create user
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Empty fields"
            )
        ));
    }
?>