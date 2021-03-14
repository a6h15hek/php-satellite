<?php 
    //imports
    include_once '../../../config/get_core.php';
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

    if(isset($_GET['start']) && isset($_GET['end'])){
        $results = $collection->get_collections($_GET['start'],$_GET['end']);
    } else if(isset($_GET['start'])){
        $results = $collection->get_collections($start=$_GET['start']);
    } else if(isset($_GET['end'])){
        $results = $collection->get_collections(0,$_GET['end']);
    }else{
        $results = $collection->get_collections();
    }
    
    print_r($results);
?>