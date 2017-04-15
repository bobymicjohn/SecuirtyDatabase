<?php
    require("config.php");
	
    function isLoggedIn()
	{
		if(empty($_SESSION['user']))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function isSessionExpired()
	{
		//TODO: In future, we will limit the time for each session.
		return false;
	}
	
	function doLogInCheck()
	{
		if(isLoggedIn())
		{
			if(isSessionExpired())
			{
				header("Location: ../index.html");
        		die("Your session has been expired! Redirecting to ../index.html");
			}
			else
			{
				//return;
			}
		}
		else
		{
			header("Location: ../index.html");
        	die("Please login first! Redirecting to ../index.html");
		}
	}
	
	function isSuperUser($UserUUID)
	{
		global $db;
		
		$query = "
		SELECT 1 
		FROM Security_Officer 
		WHERE 
			Security_Officer.Super_SSN IN 
			(SELECT SSN 
			FROM Security_Officer JOIN User_Accounts ON Security_Officer.SSN = User_Accounts.Officer_SSN 
			WHERE User_Accounts.Account_UUID = :user_UUID)
		";
	
		$query_params = array(
			':user_UUID' => $UserUUID
		);
		
		try
		{
			$stmt = $db->prepare($query);
			$qResult = $stmt->execute($query_params);
		}
    	catch(PDOException $ex)
		{ 
			die("Failed to run query: " . $ex->getMessage()); 
		}
		
		$result = false;
		if($stmt->fetch())
		{
		  $result = true;
		}
		
		return $result;
	}
	
	function isSysAdmin($UserUUID)
	{
		//TODO: We need admin account in the future.
		return false;
	}
?>