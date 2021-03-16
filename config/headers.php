<?php 
    // required headers
    if(isset($_SERVER["HTTP_ORIGIN"])){
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    }else{
        header("Access-Control-Allow-Origin: *");
    }
    header("Content-Type: application/json; charset=UTF-8");
    if($_SERVER["REQUEST_METHOD"] == "OPTIONS"){
        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        exit(0);
    }
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    //inlude the environmental varible
    require "../../../startenv.php";


    if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
        $origin = $_SERVER['HTTP_ORIGIN'];
    }else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        $origin = $_SERVER['HTTP_REFERER'];
        if(!filter_var($_ENV['ALLOW_DIRECT_URL_ACCESS'], FILTER_VALIDATE_BOOLEAN)){
            http_response_code(401);
            print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "For direct access API set ALLOW_DIRECT_URL_ACCESS=True"
                )
            ));
            exit();
        }
    } else {
        $origin = $_SERVER['REMOTE_ADDR'];
        if(!filter_var($_ENV['ALLOW_API_TESTING'], FILTER_VALIDATE_BOOLEAN)){
            http_response_code(401);
            print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "For API testing set ALLOW_API_TESTING_TOOL=True"
                )
            ));
            exit();
        }
    }
?>