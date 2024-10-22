<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Client.php';

    if(strcmp($auth_data->role, 'admin')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Only admin can create users."
            )
        ));
    }
    if(
        !empty($data->client_id) 
    ){
        // Creating a Collection Object
        $client = new Client($db);

        $client_id = $data->client_id;
        $result = $client->get_secret_key($client_id);
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