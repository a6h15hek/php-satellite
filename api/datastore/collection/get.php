<?php 
    //imports
    include_once '../../../config/get_core.php';
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
    
    $result = $collection->get_collections();
    print_r($result);
?>