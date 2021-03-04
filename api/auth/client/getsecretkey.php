<?php 
    require "../../../startenv.php";
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/Client.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    if(
        !empty($data->client_id) &&
        !empty($data->password)
    ){
        // Creating a Collection Object
        $client = new Client($db);

        $client_id = $data->client_id;
        $password = $data->password;
        $result = $client->get_secret_key($client_id,$password);
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