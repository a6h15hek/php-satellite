<?php
    include_once '../../../config/get_core.php';
    include_once '../../../models/User.php';

    // instantiate product object
    $user = new User($db);
    $user->user_id = $auth_data->user_id;
    $result = $user->getUser();
    print_r($result);

?>