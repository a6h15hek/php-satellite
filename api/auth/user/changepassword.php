<?php
   include_once '../../../config/post_core.php';
   include_once '../../../models/User.php';
   
    // create the user
    if(
        !empty($data->password) &&
        !empty($data->new_password)
    ){
         // instantiate product object
         $user = new User($db);
         // set product property values
         $user->user_id = $auth_data->user_id;
        $user->password = $data->password;
    
        $result = $user->changepassword($data->new_password);
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