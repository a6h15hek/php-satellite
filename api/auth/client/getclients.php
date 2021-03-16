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
    // Creating a Collection Object
    $client = new Client($db);

    $result = $client->getClients();
    print_r($result);
?>