<?php
	if (session_status() == PHP_SESSION_NONE) 
	{
    	session_start();
    }

	 // These variables define the connection information for your MySQL database
	$username = "jbhz";
	$password = "zxczxczxc";
	$host = "security-db-mysql.cy89i85gvki0.us-west-2.rds.amazonaws.com:3306";
	$dbname = "security";
	
	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
	try 
	{ 
		$db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); 
	}
	catch(PDOException $ex){ die("Failed to connect to the database: " . $ex->getMessage());}
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	header('Content-Type: text/html; charset=utf-8');
?>
