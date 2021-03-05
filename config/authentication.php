<?php
    //imports
    require "../../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    function checkAuth(){
        //get authorization token
        $headers = apache_request_headers();
        if(isset($headers['Authorization'])){
            $temp_array = explode(" ",$headers['Authorization']);
            $jwt_token = $temp_array[1];
            
            $result = validate_token($jwt_token);
            return $result;
        }else{
            return false;
        }
    }

    function validate_token($jwt_token){
        // if decode succeed, show user details
        try {
            // decode jwt
            $decoded = JWT::decode($jwt_token, $_ENV['JWT_KEY'], array('HS256'));

            return $decoded->data;
        }catch (Exception $e){
            // set response code
            http_response_code(401);
            return false;
        }
    }
?>