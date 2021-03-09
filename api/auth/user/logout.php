<?php
    include_once '../../../config/get_core.php';
    include_once '../../../models/User.php';

    if(!strcmp($auth_data->role, 'client')){
        http_response_code(400);
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Bad request."
            )
        ));
    }
    // instantiate product object
    $user = new User($db);
    $user->user_id = $auth_data->user_id;
    $result = $user->logout();
    print_r($result);

?>