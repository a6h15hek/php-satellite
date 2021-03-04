<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Client.php';

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