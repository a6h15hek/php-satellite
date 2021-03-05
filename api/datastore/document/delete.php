<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Document.php';

    if(
        !empty($data->collection_name) &&
        !empty($data->document_name)
    ){
         // Creating a Collection Object
        $document = new Document($db);

         //set user_id & role
         $document->user_id = $auth_data->user_id;
         $document->role = $auth_data->role;
        
        $collection_name = $data->collection_name;
        $document_name = $data->document_name;
        $result = $document->deletedocument($collection_name,$document_name);
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