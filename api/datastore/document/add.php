<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Document.php';

    // Creating a Collection Object
    if(
        !empty($data->collection_name) &&
        !empty($data->data_object)
    ){
        $document = new Document($db);

        //set user_id & role
        $document->user_id = $auth_data->user_id;
        $document->role = $auth_data->role;

        // Get raw posted data
        $collection_name = $data->collection_name;
        $data_object = $data->data_object;
        $result = $document->add($collection_name,$data_object);
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