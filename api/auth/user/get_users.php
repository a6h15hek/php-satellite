<?php
   include_once '../../../config/post_core.php';
   include_once '../../../models/User.php';
   
   if(strcmp($auth_data->role, 'admin')){
        return print_r(json_encode(
            array(
                'success'=>false,
                'message' => "Only admin can access."
            )
        ));
    }

    // instantiate product object
    $user = new User($db);

    if(isset($_GET['start']) && isset($_GET['end'])){
        $result = $user->getuserslist($_GET['start'],$_GET['end']); 
    } else if(isset($_GET['start'])){
        $result = $user->getuserslist($start=$_GET['start']);
    } else if(isset($_GET['end'])){
        $result = $user->getuserslist(0,$_GET['end']);
    }else{
        $result = $user->getuserslist(); 
    }
    print_r($result);
    
?>