<?php
    //imports
    include_once '../../../models/Client.php';
    include_once '../../../config/post_core.php';
    // get jwt
    $jwt_token=isset($data->token) ? $data->token : "";
    // if jwt is not empty
    if($jwt_token){
        // instantiate product object
        $client = new Client($db);

        $result = $client->validate_token($jwt_token);
        if($result){
            return print_r(json_encode(
                array(
                    'success'=>true,
                    'message' => "Access Granted.",
                )
            ));
        }else{
            return print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "Access Denied.",
                )
            ));
        }
    }else{
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Invalid token.",
            )
        ));
    }
?>