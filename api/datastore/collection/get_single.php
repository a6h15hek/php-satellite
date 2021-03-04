<?php 
    include_once '../../../config/get_core.php';
    include_once '../../../models/Collection.php';

    // Creating a Collection Object
    $collection = new Collection($db); 

    //Get collection Id
    if(isset($_GET['collection'])){
        $collection_name =   $_GET['collection'] ;
        //Getting collection
        $results = $collection->get_single_collections($collection_name);
        
        // printing array
        print_r($results);
    }else{
        http_response_code(400);
        print_r(json_encode(
            array(
                'success'=>false,
                'message'=>'field empty.'
            )
        ));
    }
?>