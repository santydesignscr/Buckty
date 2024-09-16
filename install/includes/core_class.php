<?php

class Core {

	// Function to validate the post data
	function validate_post($data)
	{
		// Counter variable
		$counter = 0;

		// Validate the hostname
		if(isset($data['hostname']) AND !empty($data['hostname'])) {
			$counter++;
		}
		// Validate the username
		if(isset($data['username']) AND !empty($data['username'])) {
			$counter++;
		}
		// Validate the password
		if(isset($data['password']) AND !empty($data['password'])) {
		  // pass
		}
		// Validate the database
		if(isset($data['database']) AND !empty($data['database'])) {
			$counter++;
		}

		// Check if all the required fields have been entered
		if($counter == '3') {
			return true;
		}
		else {
			return false;
		}
	}

	// Function to show an error
	function show_message($type,$message) {
		return $message;
	}

	// Function to write the config file
	function write_config($data) {

		// Config path
		$template_path 	= 'config/database.php';
		$output_path 	= '../application/config/database.php';

		// Open the file
		$database_file = file_get_contents($template_path);

		$new  = str_replace("%HOSTNAME%",$data['hostname'],$database_file);
		$new  = str_replace("%USERNAME%",$data['username'],$new);
		$new  = str_replace("%PASSWORD%",$data['password'],$new);
		$new  = str_replace("%DATABASE%",$data['database'],$new);

		// Write the new database.php file
		$handle = fopen($output_path,'w+');

		// Chmod the file, in case the user forgot
		@chmod($output_path,0777);

		// Verify file permissions
		if(is_writable($output_path)) {

			// Write the file
			if(fwrite($handle,$new)) {
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}

    function generateHtaccess($data) {
            ini_set('display_errors',1);
            $sql_path 	= '../.htaccess';
		    $output_path 	= '../.htaccess';
            $content = '';
            $content .= 'Options +FollowSymLinks -MultiViews'.PHP_EOL;
            $content .= 'RewriteEngine On'.PHP_EOL;
            $content .= 'RewriteBase /'.$data['site_folder'].PHP_EOL;
            $content .= 'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL;
            $content .= 'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL;
            $path = $data['site_folder'] != '' || NULL ? '/'.$data['site_folder'].'/index.php':'/index.php';
            $content .= 'RewriteRule ^(.*)$ '.$path.'?$1 [L]'.PHP_EOL;
            $handle = fopen($output_path,'w+');

		// Chmod the file, in case the user forgot
		@chmod($output_path,0777);

		// Verify file permissions
		if(is_writable($output_path)) {

			// Write the file
			if(fwrite($handle,$content)) {
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}
    
}