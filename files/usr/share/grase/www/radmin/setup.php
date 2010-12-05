<?php

/* Copyright 2008 Timothy White */

/* NO LONGER USING THIS. TO BE CLEANED UP OR REMOVED LATER */

/* Initial setup page */

// Report simple running errors
// TODO: set this for release
//error_reporting(E_ERROR);

require_once 'includes/constants.inc.php';
//TODO: load all this stuff in another file that has no DB/config file dependencies (and smarty stuff in it too)
$config_file = dirname(__FILE__).'/configs/site.conf';
$config_dir = dirname($config_file);

require_once 'libs/Smarty.class.php';

require_once 'includes/misc_functions.inc.php';
require_once 'includes/auth_functions.inc.php';

$smarty = new Smarty;
$smarty->compile_check = true;
$smarty->assign("Title", "Inital Setup - " . APPLICATION_NAME);
$smarty->assign("Application", APPLICATION_NAME);

$smarty->assign("Script_user", getmyuid().":".getmygid());


///

if($_POST['setup1submit']){
	// Form submitted, process
	if(!check_write_file($_POST["admin_user_passwd_file"])){
		$smarty->assign("admin_user_passwd_file_error", "File is not writeable by the webserver and will need to be manually created.");
	}

	if(!check_write_file($config_file)){
		$smarty->assign("config_file_error", "File is not writeable by the webserver and will need to be manually created.");
	}

	if(strlen(trim($_POST["location_name"])) <= 1){
		$smarty->assign("location_name_error", "You need a location name for this installation");
		$error = true;
	}

	if(strlen(trim($_POST["admin_user"])) < 3){
		$smarty->assign("admin_user_error", "Username not long enough");
		$error = true;
	}
	if(strlen(trim($_POST["admin_user_passwd"])) < 5){
		$smarty->assign("admin_user_passwd_error", "Password not long enough");
		$error = true;
	}

	if(!check_write_file($_POST["database_config_file"])){
		$smarty->assign("database_config_file_error", "File is not writeable by the webserver and will need to be manually created.");
	}

	if(strlen(trim($_POST["sql_server"])) < 1){
		$smarty->assign("sql_server_error", "Need a server defined");
		$error = true;
	}

	if(strlen(trim($_POST["sql_root_username"])) < 1){
		$smarty->assign("sql_root_username_error", "Without a poweruser you may need to manually create the database. If the database and user are already created, ignore this.");
	}

/*	if(strlen(trim($_POST["sql_password"])) < 1){
		$smarty->assign("sql_password_error", "A blank password is not recommended");
	}*/


	if(strlen(trim($_POST["sql_username"])) < 1){
		$smarty->assign("sql_username_error", "Need a username");
		$error = true;
	}

	if(strlen(trim($_POST["sql_password"])) < 1){
		$smarty->assign("sql_password_error", "A blank password is not recommended");
	}

	if(strlen(trim($_POST["sql_database"])) < 1){
		$smarty->assign("sql_database_error", "Need a database");
		$error = true;
	}

	// 
	$smarty->assign("config_file", $config_file);
	$smarty->assign("location_name", $_POST["location_name"]);			
	$smarty->assign("admin_user_passwd_file", $_POST["admin_user_passwd_file"]);	
	$smarty->assign("admin_user", $_POST["admin_user"]);
	$smarty->assign("admin_user_passwd", $_POST["admin_user_passwd"]);
	$smarty->assign("database_config_file", $_POST["database_config_file"]);	
	$smarty->assign("sql_root_server", $_POST["sql_root_server"]);
	$smarty->assign("sql_root_username", $_POST["sql_root_username"]);
	$smarty->assign("sql_server", $_POST["sql_server"]);
	$smarty->assign("sql_username", $_POST["sql_username"]);
	$smarty->assign("sql_password", $_POST["sql_password"]);
	$smarty->assign("sql_database", $_POST["sql_database"]);

	$smarty->assign("CONFIG", true);	
	// if no errors, then continue so CONFIG true
	if($error){
		$smarty->assign("error", "You have errors, please fix them and then attempt to regenerate");
	}else{
		$siteconfig = generate_siteconfig($_POST["location_name"], $_POST["admin_user_passwd_file"], $_POST["database_config_file"]);
		$smarty->assign("siteconfig", $siteconfig);
		if(check_write_file($config_file) && file_put_contents($config_file, $siteconfig)) $siteconfig_file_written = true;
		$smarty->assign("siteconfig_file_written", $siteconfig_file_written);

		$userlogins[$_POST["admin_user"]] = generateHash($_POST["admin_user_passwd"]);
		$admin_user_config = generate_admin_users_passwd_file($userlogins);
		$smarty->assign("admin_user_config", $admin_user_config);
		if(check_write_file($_POST["admin_user_passwd_file"]) && file_put_contents($_POST["admin_user_passwd_file"], $admin_user_config)) $admin_user_file_written = true;
		$smarty->assign("admin_user_file_written", $admin_user_file_written);

		$database_config = generate_database_config_file($_POST["sql_server"], $_POST["sql_username"], $_POST["sql_password"], $_POST["sql_database"]);
		$smarty->assign("database_config", $database_config);
		if(check_write_file($_POST["database_config_file"]) && file_put_contents($_POST["database_config_file"], $database_config)) $database_config_file_written = true;
		$smarty->assign("database_config_file_written", $database_config_file_written);

		// Config files generated ^^
		// Move on to database things

	}

}else{
	// Defaults
	$smarty->assign("CONFIG", false);	
	// Config file is invalid, use defaults
	$smarty->assign("config_file", $config_file);
	$smarty->assign("location_name", "Hotspot1");		
	$smarty->assign("admin_user_passwd_file", "/home/".get_current_user()."/private/admin_users");	
	$smarty->assign("admin_user", "admin");
	$smarty->assign("admin_user_passwd", rand_password(6));
	$smarty->assign("database_config_file", "/etc/radmin.conf");	
	$smarty->assign("sql_server", "localhost");
	$smarty->assign("sql_username", get_current_user());
	$smarty->assign("sql_password", rand_password(6));
	$smarty->assign("sql_database", "radius");
}






/*$smarty->assign("config_dir_check", check_config_dir($config_dir));
$smarty->assign("config_dir", "configs/");
$smarty->assign("config_file_check", check_config_file($config_file));
$smarty->assign("config_file", "configs/site.conf");


// Options in config_file
list($CONFIG, $config_options) = load_config_file($config_file);

if($CONFIG){
	// Config file is valid and loaded
	// Check each option in CONFIG
	$smarty->assign("CONFIG", true);	

	// Admin User File
	$error = false;
	if($option["admin_user_passwd_file_in_config"]){
		$admin_user_passwd_file = $CONFIG['admin_users_passwd_file'];
		if(!$option["admin_user_passwd_file_writeable"]) $error = "'$admin_user_passwd_file' needs to be writeable by the webserver";
		if(!$option["admin_user_passwd_file_exists"]) $error = "'$admin_user_passwd_file' does not exist. Please create the file and make it writeable by the webserver.";
	}else{
		$admin_user_passwd_file = "";
		$error = "Admin User Passwd File does not exists";
	}
	$smarty->assign("admin_user_passwd_file_error", $error);
	$smarty->assign("admin_user_passwd_file", $admin_user_passwd_file);	




}else{
	$smarty->assign("CONFIG", false);	
	// Config file is invalid, use defaults
	$smarty->assign("admin_user_passwd_file", "");	
	$smarty->assign("database_config_file", "/etc/radmin.conf");	

}
*/

$smarty->display('setup1.tpl');



/* Setup DB
 * Setup Users
 * Setup Config */


/* Folder write checks */
function check_config_dir($config_dir){
	if(file_exists($config_dir)) return false;
	return true;
}

function check_write_file($config_file){
	$config_dir = dirname($config_file);
	if(file_exists($config_file) && is_writeable($config_file)) return true;
	if(!file_exists($config_file) && file_exists($config_dir) && is_writeable($config_dir) && file_put_contents($config_file, " ")) return true;
	return false;
}


function load_config_file($config_file){
	$option = array();
	if(is_file($config_file)){
		// Parse Config File
		$CONFIG = parse_ini_file($config_file);

		// Check config

		// $CONFIG['admin_users_passwd_file'];
		if(isset($CONFIG['admin_users_passwd_file'])) {
			$option["admin_user_passwd_file_in_config"] = true;
			if(is_file($CONFIG['admin_users_passwd_file'])) $option["admin_user_passwd_file_exists"] = true;
			if(is_writeable($CONFIG['admin_users_passwd_file'])) $option["admin_user_passwd_file_writeable"] = true;
		}
		// $CONFIG['database_config_file'];
		if(isset($CONFIG['database_config_file'])) {
			$option["database_config_file_in_config"] = true;
			if(is_file($CONFIG['database_config_file'])) $option["database_config_file_exists"] = true;
		}
		return array($CONFIG, $option);
	}else{
		return array(false, false);
	}
}

/// Generating functions

function generate_admin_users_passwd_file($userlogins){
	return serialize($userlogins);
}

function generate_database_config_file($server, $username, $password, $database){
	$databaseconfig = <<<EDBCON
sql_type: mysql
sql_server: $server
sql_username: $username
sql_password: $password
sql_database: $database
sql_command: /usr/bin/mysql

EDBCON;
	return $databaseconfig;
}

function generate_siteconfig($location_name, $admin_users_passwd_file, $database_config_file, $total_sellable_data = "2000000000", $total_useable_data = "3000000000"){
	$siteconfig = <<<ESCON
location_name = $location_name

; Set admin_users_passwd_file to a file outside of the web server, but writeable by the webserver
admin_users_passwd_file = "$admin_users_passwd_file"

; Database config file (copy and edit configs/radmin.conf.example to a location outside of the webserver and edit database_config_file to reflect this move)
; NB: Most scripts in the scripts directory look for radmin.conf in /etc/radmin.conf
database_config_file = "$database_config_file"

; 2000000000 = 2Gb
total_sellable_data = $total_sellable_data
total_usable_data = $total_useable_data

ESCON;
	return $siteconfig;
}

function create_database($username, $password, $server, $database, $poweruser = "", $powerpass = ""){
	if(!$poweruser){
		$connect_username = $username;
		$connect_password = $password;
		// Test to see if they can connect
		// Test if db is already there
		// Create database
	}else{
		$connect_username = $poweruser;
		$connect_password = $powerpass;
		// Test to see if they can connect
		// Test if db is already there
		// Add user
		// Create database
	}
	
	// Connect
	$dblink = $mysql_pconnect ($server, $connect_username, $connect_password);
	if(!$dblink){ // Connection failed
		if(!$poweruser){ // Connection failed and not using poweruser settings
			$error = "Connection failed. $connect_username:$connect_password is invalid for MySQL server '$server'. Please manually create the database and user or fill in MySQL Poweruser details and retry.";
		}else{
			$error = "Connection failed. $connect_username:$connect_password is invalid for MySQL server '$server'. Please manually create the database and user.";
		}
		return $error;
	}
	
	if(!mysql_select_db($database)){ // Database select failed
		// Attempt to create database
		$sql = "CREATE DATABASE IF NOT EXISTS ".mysql_real_escape_string($database);
		if (!mysql_query($sql, $dblink)) {
			// Failed to create database
			$error = "Connected to server as $connect_username succeded. Unable to create or use $database database. Please manually create database or grant $connect_username permission to use it.";
			return $error;
		}
		if($connect_username != $username){ // Need to grant $username privs
			$sql = "GRANT ALL ON ".mysql_real_escape_string($database).".* TO '".mysql_real_escape_string($username)."'  IDENTIFIED BY '".mysql_real_escape_string($password)."'";
/////////////////////// TODO: TODO: Working HERE
		}
	}




	// Return true for any errors and display manual code required

}

function generate_manual_database_create($username, $password, $database){
	// TODO: set a server to limit the user?
	$database_create = <<<EDBCREATE
CREATE DATABASE IF NOT EXISTS $database;
GRANT ALL ON $database.* TO '$username'  IDENTIFIED BY '$password';
EDBCREATE;
	return $database_create;
}


?>
