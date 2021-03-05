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
   include_once '../../../models/User.php';

   // Initialize database 
   $database = new Database();
   $db = $database->connect();

   // Get raw posted data
   $data = json_decode(file_get_contents("php://input"));
    // create the user
    if(
        !empty($data->firstname) &&
        !empty($data->email) &&
        !empty($data->password)
    ){
         // instantiate product object
         $user = new User($db);
         // set product property values
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        
        if($user->emailExists()){
            http_response_code(409);
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