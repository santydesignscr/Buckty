<?php

if ($_POST) {
    require_once('includes/core_class.php');
    require_once('includes/database_class.php');
    $core = new Core();
    $database = new Database();


// Validate the post data
    if ($core->validate_post($_POST) == true) {
        if($database->addAdmin($_POST)){
            $message = array('error_code'=>0,'message'=> 'Admin user was created');
            echo json_encode($message);
        } else {
            $message = array('error_code'=>1,'message'=> 'Problem With fields');
            echo json_encode($message);
        }
    } else {
        $message = array('error_code'=>1,'message'=> 'Not all fields have been filled in correctly. The host, username, password, and database name are required.');
        echo json_encode($message);
    }
}