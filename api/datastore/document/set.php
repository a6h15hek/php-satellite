<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Document.php';

    if(
        !empty($data->collection_name) &&
        !empty($data->document_name) &&
        !empty($data->data_object)
    ){
         // Creating a Collection Object
        $document = new Document($db);
        
        $collection_name = $data->collection_name;
        $document_name = $data->document_name;
        $data_object = $data->data_object;
        if(isset($data->merge)){
            $result = $document->set($collection_name,$document_name,$data_object,$data->merge);
        }else{
            $result = $document->set($collection_name,$document_name,$data_object);
        }
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