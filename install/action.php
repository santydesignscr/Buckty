<?php

if ($_POST) {
    require_once('includes/core_class.php');
    require_once('includes/database_class.php');
    $core = new Core();
    $database = new Database();


// Validate the post data
    if ($core->validate_post($_POST) == true) {

        if ($database->create_database($_POST) == false) {
            $message = array('error_code'=>1,'message'=>"The database could not be created, please verify your settings.");
            echo json_encode($message);
        } else if ($database->create_tables($_POST) == false) {
            $message = array('error_code'=>1,'message'=>"The database tables could not be created, please verify your settings.");
            echo json_encode($message);
        } else if ($core->write_config($_POST) == false) {
            $message = array('error_code'=>1,'message'=> "The database configuration file could not be written, please chmod application/config/database.php file to 777");
            echo json_encode($message);
        }
        if($core->generateHtaccess($_POST) == false){
            $message = array('error_code'=>1,'message'=> "There was a problem while generating htaccess file");
            echo json_encode($message);
        }

// If no errors, redirect to registration page
        if (!isset($message)) {
            $message = array('error_code'=>0,'message'=> 'Database loaded.');
            echo json_encode($message);
        }

    } else {
        $message = array('error_code'=>1,'message'=> 'Not all fields have been filled in correctly. The host, username, password, and database name are required.');
        echo json_encode($message);
    }
}