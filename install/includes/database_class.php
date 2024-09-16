<?php

class Database {

	// Function to the database and tables and fill them with the default data
	function create_database($data)
	{
		// Connect to the database
		$mysqli = new mysqli($data['hostname'],$data['username'],$data['password'],'');

		// Check for errors
		if(mysqli_connect_errno())
			return false;

		// Create the prepared statement
		$mysqli->query("CREATE DATABASE IF NOT EXISTS ".$data['database']);

		// Close the connection
		$mysqli->close();

		return true;
	}

	// Function to create the tables and fill them with the default data
	function create_tables($data)
	{
		// Connect to the database
		$mysqli = new mysqli($data['hostname'],$data['username'],$data['password'],$data['database']);

		// Check for errors
		if(mysqli_connect_errno())
			return false;

        $sql_path 	= 'database/install.sql';

        // Open the file
        $sql_file = file_get_contents($sql_path);

        $new  = str_replace("%SITE_URL%",$data['site_url'],$sql_file);
        $new  = str_replace("%SITE_NAME%",$data['site_name'],$new);
        $new  = str_replace("%SITE_ADMIN_EMAIL%",$data['site_admin_email'],$new);
        $new  = str_replace("%SITE_KEYWORDS%",$data['site_keywords'],$new);
        $new  = str_replace("%SITE_DESCRIPTION%",$data['site_description'],$new);
        $new  = str_replace("%SITE_UPLOAD_LIMIT%",$data['site_upload_limit'],$new);
        $new  = str_replace("%SITE_MAX_FILE_SIZE%",$data['site_max_file_size'],$new);
        $extensions = serialize(explode(',',$data['site_allowed_extensions']));
        $d_extensions = serialize(explode(',',$data['site_blacklist_extensions']));
        $new  = str_replace("%SITE_ALLOWED_EXTENSIONS%",$extensions,$new);
        $new  = str_replace("%SITE_BLACKLIST_EXTENSIONS%",$d_extensions,$new);
        $new  = str_replace("%SITE_HOME_TAGLINE%",$data['site_home_tagline'],$new);
        $new  = str_replace("%SITE_HOME_DESCRIPTION%",$data['site_home_description'],$new);
        $new  = str_replace("%SITE_SMTP_HOST%",$data['site_smtp_host'],$new);
        $new  = str_replace("%SITE_SMTP_PORT%",$data['site_smtp_port'],$new);
        $new  = str_replace("%SITE_SMTP_USER%",$data['site_smtp_user'],$new);
        $new  = str_replace("%SITE_SMTP_PASSWORD%",$data['site_smtp_password'],$new);
        $disqus = serialize(array('shortname'=>$data['site_disqus_shortname']));
        $query = str_replace("%SITE_DISQUS_SHORTNAME%",$disqus,$new);

		// Execute a multi query
		$mysqli->multi_query($query);

		// Close the connection
		$mysqli->close();

		return true;
	}
    
    function addAdmin($data){
        $username = $data['user'];
        $passw = $data['passw'];
        $email = $data['email'];
        if(!empty($username) && !empty($email) && !empty($passw) && strlen($passw) <= 15){
            $mysqli = new mysqli($data['hostname'],$data['username'],$data['password'],$data['database']);
		if(mysqli_connect_errno())
            return false;
            
            $password = md5($passw);
            $mysqli->query("INSERT INTO buckty_users (`email`,`name`,`pass`) VALUES ('$email','$username','$password')");
            $user_id = (int) $mysqli->insert_id;
            $password = $this->hash_password($passw,$user_id);
            $hash = $this->generateRandomString();
            $mysqli->query("INSERT INTO buckty_user_variables (`id`,`user_id`, `data_key`, `value`) VALUES (NULL,$user_id,'user_hash','$hash')");
             $mysqli->query("INSERT INTO buckty_user_to_group (`user_id`, `group_id`) VALUES ($user_id,1)");
             $mysqli->query("UPDATE buckty_users SET pass = '$password' WHERE id = ".$user_id);
            return true;
        } else {
            return false;
        }
    }
    
    function hash_password($pass, $userid) {

		$salt = md5($userid);
		return hash('sha256',$salt.$pass);
	}
    
    function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
    }
}