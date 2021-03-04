<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Document.php';

    if(
        !empty($data->collection_name) &&
        !empty($data->arrayfield) &&
        !empty($data->arrayelement) &&
        !empty($data->document_name)
    ){
        // Creating a Collection Object
        $document = new Document($db);

        $collection_name = $data->collection_name;
        $document_name = $data->document_name;
        $arrayfield = $data->arrayfield;
        $arrayelement = $data->arrayelement;

        if(isset($_GET['action'])){
            $result = $document->updatearray($collection_name,$document_name,$arrayfield,$arrayelement,$_GET['action']);
        }else{
            $result = $document->updatearray($collection_name,$document_name,$arrayfield,$arrayelement);
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