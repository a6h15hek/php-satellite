<?php 
    include_once '../../../config/get_core.php';
    include_once '../../../models/Document.php';

     

    //Get collection Id
    if(isset($_GET['collection']) && isset($_GET['document'])){
        // Creating a Collection Object
        $document = new Document($db);

         //set user_id & role
         $document->user_id = $auth_data->user_id;
         $document->role = $auth_data->role;
        
        $collection_name =   $_GET['collection'] ;
        $document_name =   $_GET['document'] ;
        //Getting collection
        $results = $document->get($collection_name, $document_name);
        
        // printing array
        print_r($results);
    }else{
        http_response_code(400);
        print_r(json_encode(
            array(
                'success'=>false,
                'message'=>'collection not specified or document not specified.'
            )
        ));
    }
?>