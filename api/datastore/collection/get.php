<?php 
    //imports
    include_once '../../../config/get_core.php';
    include_once '../../../models/Collection.php';

    // Creating a Collection Object
    $collection = new Collection($db);
    
    $result = $collection->get_collections();
    print_r($result);
?>