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
    // Creating a Collection Object
    $collection = new Collection($db);

    if(
        !empty($data->collection_name) &&
        (!empty($data->read) || !empty($data->write))
    ){
        $collection_name = $data->collection_name;

        if(isset($data->read) && isset($data->write)){
            $result = $collection->updatepermissions($collection_name, $data->read, $data->write);
        } else if(isset($data->read)){
            $result = $collection->updatepermissions($collection_name, $data->read, NULL);
        } else if(isset($data->write)){
            $result = $collection->updatepermissions($collection_name, NULL, $data->write);
        }
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