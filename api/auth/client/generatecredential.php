<?php 
    require "../../../startenv.php";
    include_once "../../../config/headers.php";

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/Client.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    if(
        !empty($data->password)
    ){
        // Creating a Collection Object
        $client = new Client($db);

        $password = $data->password;
        $result = $client->generate_client_id_password($password);
        print_r($result);
    }else{
         // set response code
         http_response_code(400);
    
         // display message: unable to create user
         return print_r(json_encode(
             array(
                 'success'=>false,
                 'message' => "Empty fields."
             )
         ));
    }
?>