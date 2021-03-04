<?php
    //imports
    include_once '../../../models/User.php';
    include_once '../../../config/post_core.php';
    // get jwt
    $jwt_token=isset($data->token) ? $data->token : "";
    // if jwt is not empty
    if($jwt_token){
        // instantiate product object
        $user = new User($db);

        $result = $user->validate_token($jwt_token);
        print_r($result);
        // catch will be here
    }else{
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Invalid token.",
            )
        ));
    }
?>