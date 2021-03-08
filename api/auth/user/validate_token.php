<?php
    include_once '../../../config/get_core.php';
    include_once '../../../config/authentication.php';
    include_once '../../../models/User.php';

    //get authorization token
    $headers = apache_request_headers();
    if(isset($headers['Authorization'])){
        $temp_array = explode(" ",$headers['Authorization']);
        $jwt_token = $temp_array[1];

        $user = new User($db);
        $result = $user->validate_token($jwt_token);
        if($result){
            http_response_code(200);
            return print_r(json_encode(
                array(
                    'success'=>true,
                    'message' => "Access Granted.",
                    'data' => $result,
                )
            ));
        }else{
            http_response_code(401);
            return print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "Access Denied.",
                )
            ));
        }

    }else{
        http_response_code(400);
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Authorization token empty.",
            )
        ));
    }
?>