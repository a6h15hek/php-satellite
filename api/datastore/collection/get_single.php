<?php 
    require "../../../startenv.php";
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/Collection.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

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
        print_r(json_encode(
            array(
                'success'=>false,
                'message'=>'collection not specified'
            )
        ));
    }
?>