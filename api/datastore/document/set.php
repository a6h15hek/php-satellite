<?php 
    require "../../../startenv.php";
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/Document.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

    // Creating a Collection Object
    $document = new Document($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));
    
    if($data === "" || $data === null){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "collections name not defined or dataobject not defined"
            )
        ));
    }
    
    $collection_name = $data->collection_name;
    $document_name = $data->document_name;
    $data_object = $data->data_object;
    if(isset($data->merge)){
        $result = $document->set($collection_name,$document_name,$data_object,$data->merge);
    }else{
        $result = $document->set($collection_name,$document_name,$data_object);
    }
    print_r($result);
?>