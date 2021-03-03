<?php 
    require "../../../startenv.php";
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/Document.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

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