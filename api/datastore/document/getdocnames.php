<?php 
    include_once '../../../config/get_core.php';
    include_once '../../../models/Document.php';

    if(strcmp($auth_data->role, 'admin')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Only admin can access."
            )
        ));
    }

    // Creating a Collection Object
    $document = new Document($db); 

    $document->role = $auth_data->role;
    
    //Get collection Id
    if(isset($_GET['collection'])){
        $collection_name =   $_GET['collection'] ;
        //Getting collection
        if(isset($_GET['start']) && isset($_GET['end'])){
            $results = $document->getDocNames($collection_name,$_GET['start'],$_GET['end']);
        } else if(isset($_GET['start'])){
            $results = $document->getDocNames($collection_name,$start=$_GET['start']);
        } else if(isset($_GET['end'])){
            $results = $document->getDocNames($collection_name,0,$_GET['end']);
        }else{
            $results = $document->getDocNames($collection_name);
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