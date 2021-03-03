<?php 
    require "../../../startenv.php";
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

    //imports
    include_once '../../../config/Database.php';
    include_once '../../../models/Collection.php';

    // Initialize database 
    $database = new Database();
    $db = $database->connect();

    // Creating a Collection Object
    $collection = new Collection($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));
    
    if($data === "" || $data === null){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "collection name is null or undefined or empty."
            )
        ));
    }
    $collection_name = $data->collection_name;
    $result = $collection->create($collection_name);
    print_r($result);
?>