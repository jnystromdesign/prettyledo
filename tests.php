<?php
	
	// 	Well, I like sharing but there's a limit for everything... 
	require_once('../app_prettyledo_config.php');
	
	//	Setting up varibles from config 
	$myAppId = MY_APP_ID;
	$myAppToken = MY_APP_TOKEN;
	$userEmail = USER_EMAIL;
	$userPassword = USER_PASSWORD;
	
	
	function curlThis($url){
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$output = curl_exec($ch);

		curl_close($ch);
		
		$object = json_decode($output, TRUE);
				
		return $object; 
	}
	
	// Set up signature key for signing stuff...
	function setUserID($myAppId, $userEmail, $userPassword)
	{
		/*--- Account Lookup ---*/
		global $userEmail, $myAppToken;
		
		$sig = md5($userEmail.$myAppToken );
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
	
	function createSessionToken()
	{
		global $myAppToken, $myAppId;
		$userid = $_COOKIE['userid'];
		$key = md5($userid.$myAppToken);
		$url = 'http://api.toodledo.com/2/account/token.php?userid=' . $userid . ';appid=' . $myAppId . ';sig=' . $key;
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$output = curl_exec($ch);

		curl_close($ch);

		$object = json_decode($output);
		
		return $object;
	}
	
	function createKey($userPassword, $appToken, $sessionToken){
		$key = md5( md5($userPassword).$appToken.$sessionToken);
		return $key;
	}
	
	function unsetUserId()
	{
		unset($_COOKIE['userid']);
	}
	
	
	
	// Run only if cookie isn't set when user haven't asked to log out.
	if(!isset($_COOKIE['userid']) && !isset($_GET['logout'])){
		setUserID($myAppId, $userEmail, $userPassword);
	}
	
	// Run if the user Id is set and if no authtoken is set. (We'll get capped if we require more than 10 token per hours...)
	if(isset($_COOKIE['userid'])){
		if(!isset($_COOKIE['sessionToken'])){
			$authObject = createSessionToken();
			setCookie('sessionToken', $authObject->token);
		}
	}
	
	
	// Run if user choosed to log out.
	if(isset($_GET['logout'])){
		unsetUserId();
	}
	/*
	$apiKey = 'c3c094da7058293e58aa94e67ab4c661';
	$method = 'flickr.photos.search';
	$text = 'cat';
	$url= "http://api.flickr.com/services/rest/?method=$method&api_key=$apiKey&text=$text&format=json&nojsoncallback=1&per_page=20";
	*/ 
	$sessionToken = $_COOKIE['sessionToken'];
	$key = createKey($userPassword, $myAppToken, $sessionToken);
	$url = "http://api.toodledo.com/2/tasks/get.php?key=$key;modafter=1234567890;fields=folder,star,priority";
	$object = curlThis($url);
	
	print $url;
	print '<pre>';
	print_r($object);
	print '</pre>';


	
	