<?php 
    include_once '../../../config/post_core.php';
    include_once '../../../models/Collection.php';

    if(strcmp($auth_data->role, 'admin')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "You need admin level permissions."
            )
        ));
    }
    
    if(
        !empty($data->collection_name)
    ){
        // Creating a Collection Object
        $collection = new Collection($db);

        $collection_name = $data->collection_name;
        
        //check input variable
        if(isset($data->read) && isset($data->write)){
            $result = $collection->create($collection_name, $data->read, $data->write);
        } else if(isset($data->read)){
            $result = $collection->create($collection_name, $data->read, "private");
        } else if(isset($data->write)){
            $result = $collection->create($collection_name, "private", $data->write);
        }else{
            $result = $collection->create($collection_name);
        }
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