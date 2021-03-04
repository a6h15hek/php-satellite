<?php 
    include_once '../../../config/get_core.php';
    include_once '../../../models/Document.php';

    // Creating a Collection Object
    $document = new Document($db); 

    //Get collection Id
    if(isset($_GET['collection']) && isset($_GET['document'])){
        $collection_name =   $_GET['collection'] ;
        $document_name =   $_GET['document'] ;
        //Getting collection
        $results = $document->get($collection_name, $document_name);
        
        // printing array
        print_r($results);
    }else{
        print_r(json_encode(
            array(
                'success'=>false,
                'message'=>'collection not specified or document not specified.'
            )
        ));
    }
?>