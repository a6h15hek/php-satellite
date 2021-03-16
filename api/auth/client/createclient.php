<?php
   include_once '../../../config/post_core.php';
   include_once '../../../models/Client.php';
   
   if(strcmp($auth_data->role, 'admin')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Only admin can create client."
            )
        ));
    }
    // create the user
    if(
        !empty($data->app_name)
    ){
         // instantiate product object
         $client = new Client($db);
         // set product property values
        $client->app_name = $data->app_name;
        
        $result = $client->createClient();
        print_r($result);
    }
    
    // message if unable to create user
    else{
    
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