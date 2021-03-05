<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Collection.php';

    if(!strcmp($auth_data->role, 'user')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "You need admin level permissions."
            )
        ));
    }
    // Creating a Collection Object
    $collection = new Collection($db);

    if(
        !empty($data->collection_name) &&
        !empty($data->new_collection_name)
    ){
        $collection_name = $data->collection_name;
        $new_collection_name = $data->new_collection_name;
        $result = $collection->update($collection_name,$new_collection_name);
        print_r($result);
    }else{
         // set response code
         http_response_code(400);
    
         // display message: unable to create user
         return print_r(json_encode(
             array(
                 'success'=>false,
                 'message' => "Empty fields."
             )
         ));
    }
?>