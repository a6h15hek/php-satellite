<?php
   include_once '../../../config/post_core.php';
   include_once '../../../models/User.php';
   
   if(!strcmp($auth_data->role, 'client')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Only client can create users."
            )
        ));
    }
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