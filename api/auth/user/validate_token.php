<?php
    require "../../../startenv.php";
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // database connection will be here
    require "../../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));
        
    if($data === "" || $data === null){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "collections name not defined or dataobject not defined"
            )
        ));
    }

    // get jwt
    $jwt_token=isset($data->token) ? $data->token : "";
    // if jwt is not empty
    if($jwt_token){
        // if decode succeed, show user details
        try {
            // decode jwt
            $decoded = JWT::decode($jwt_token,$_ENV['JWT_KEY'], array('HS256'));
    
            // set response code
            http_response_code(200);
    
            // show user details
            echo json_encode(array(
                "message" => "Access granted.",
                "data" => $decoded->data
            ));
    
        }catch (Exception $e){
 
            // set response code
            http_response_code(401);
         
            // tell the user access denied  & show error message
            echo json_encode(array(
                "success" => false,
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
    
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