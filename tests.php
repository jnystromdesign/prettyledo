<?php
	
	// 	Well, I like sharing but there's a limit for everything... 
	require_once('../app_prettyledo_config.php');
	
	//	Setting up varibles from config 
	$myAppId = MY_APP_ID;
	$myAppToken = MY_APP_TOKEN;
	$userEmail = USER_EMAIL;
	$userPassword = USER_PASSWORD;
	
	
	
	// Set up signature key for signing stuff...
	
	//  BUG - Can't resolve a response.... :( Seems like I'm capped. :o
	function authentication()
	{
		global $myAppToken, $myAppId;
		
		$userid = $_COOKIE['userid'];
		$key = md5($userid.$myAppToken);
		$url = 'http://api.toodledo.com/2/account/token.php?userid=' . $userid . ';appid=' . $myAppId . ';sig=' . $key;
		
		print $key .'<br>';
		print $userid .'<br>';
		print $url . '<br>';
		
		
		/*
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$output = curl_exec($ch);

		curl_close($ch);

		$object = json_decode($output);
		
		print '<pre>';
		print_r($object);
		print '</pre>';
		*/
	}
	
	
	authentication();
	
	function setUserID()
	{
		/*--- Account Lookup ---*/
		global $userEmail, $myAppToken;
		
		$sig = md5($userEmail.$myAppToken );
		print '<pre>';
		print_r($sig);
		print '</pre>';
		$ch = curl_init();
		$url = "http://api.toodledo.com/2/account/lookup.php?appid=$myAppId;sig=$sig;email=$userEmail;pass=$userPassword";

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$output = curl_exec($ch);

		curl_close($ch);

		$object = json_decode($output);
		$userID = $object->userid; 
		setCookie('userid', $userID);

	}
	
	function unsetUserId()
	{
		unset($_COOKIE['userid']);
	}
	
	
	
	// Run only if cookie isn't set when user haven't asked to log out.
	if(!isset($_COOKIE['userid']) && !isset($_GET['logout'])){
		setUserID();
	}
	
	// Run if user choosed to log out.
	if(isset($_GET['logout'])){
		unsetUserId();
	}
	


	
	