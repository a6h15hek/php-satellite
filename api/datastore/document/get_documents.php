<?php 
    include_once '../../../config/get_core.php';
    include_once '../../../models/Document.php';

    // Creating a Collection Object
    $document = new Document($db); 

    //Get collection Id
    if(isset($_GET['collection'])){
        $collection_name =   $_GET['collection'] ;
        //Getting collection
        if(isset($_GET['start']) && isset($_GET['end'])){
            $results = $document->getdocuments($collection_name,$_GET['start'],$_GET['end']);
        } else if(isset($_GET['start'])){
            $results = $document->getdocuments($collection_name,$start=$_GET['start']);
        } else if(isset($_GET['end'])){
            $results = $document->getdocuments($collection_name,0,$_GET['end']);
        }else{
            $results = $document->getdocuments($collection_name);
        }
        // printing array
        print_r($results);
    }else{
        http_response_code(400);
        return print_r(json_encode(
            array(
                'success'=>false,
                'message'=>'collection not specified'
            )
        ));
    }
?>