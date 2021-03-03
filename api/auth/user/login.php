<?php
    require "../../../startenv.php";
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // database connection will be here
    require "../../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

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
    $user->email = $data->email;
    $email_exists = $user->emailExists();

    // check if email exists and if password is correct
    if($email_exists && password_verify($data->password, $user->password)){
    
        $token = array(
        "iat" => time(),
        "iss" =>  $_ENV['JWT_ISSUER'],
        "data" => array(
                "user_id" => $user->user_id,
                "email" => $user->email,
                "role" => $user->role
            )
        );
    
        // generate jwt
        $jwt = JWT::encode($token, $_ENV['JWT_KEY']);
        return print_r(json_encode(
            array(
                'success'=>true,
                'message' => "Login successfully.",
                'token' => $jwt
            )
        ));
    }else{
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "email or password incorrect."
            )
        ));
    }

?>