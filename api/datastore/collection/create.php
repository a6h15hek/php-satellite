<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Collection.php';

    if(
        !empty($data->collection_name)
    ){
        // Creating a Collection Object
        $collection = new Collection($db);

        $collection_name = $data->collection_name;
        $result = $collection->create($collection_name);
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