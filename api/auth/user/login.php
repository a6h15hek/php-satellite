<?php
   
    
    // database connection will be here
    require "../../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    //imports
    include_once '../../../config/post_core.php';
    include_once '../../../models/User.php';

    if(
        !empty($data->email) &&
        !empty($data->password)
    ){
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
    }else{
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