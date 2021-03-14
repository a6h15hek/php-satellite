<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/User.php';

    // Creating a Collection Object
    $user = new User($db);

    if(
        !empty($data->user_id)
    ){
        if(!strcmp($auth_data->role, "admin")){
            $user->user_id = $data->user_id;
        }else{
            $user->user_id = $auth_data->user_id;
        }
        $result = $user->delete();
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