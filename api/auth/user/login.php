<?php
    include_once '../../../config/post_core.php';
    include_once '../../../models/User.php';

    if(!strcmp($auth_data->role, 'client')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Only client can request to login user."
            )
        ));
    }
    if(
        !empty($data->email) &&
        !empty($data->password)
    ){
        // instantiate product object
        $user = new User($db);

        $result = $user->login($data->email,$data->password);
        print_r($result);
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