<?php
    include_once '../../../config/post_core.php';
    include_once '../../../models/User.php';

    // instantiate product object
    $user = new User($db);
    $user->user_id = $auth_data->user_id;
    $result = $user->logout();
    print_r($result);

?>