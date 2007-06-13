<?php
/**************************************************************

    __  ___              __                        __ __
   /  |/  /__  __ _____ / /_ ___   _____ __  __   / // /
  / /|_/ // / / // ___// __// _ \ / ___// / / /  / // /_
 / /  / // /_/ /(__  )/ /_ /  __// /   / /_/ /  /__  __/
/_/  /_/ \__, //____/ \__/ \___//_/    \__, /     /_/
        /____/                        /____/

Mystery 4.0.0

Developed by Paul Burney
Web: paulburney.com
AIM: PWBurney
E-mail: support@paulburney.com

***************************************************************

custom/authentication.php

This is the custom authentication function file.  It contains the
custom functions used for authentication.  Mystery authentication
is very flexible and 

**************************************************************/

function mystery_auth_function_template($username, $password) {

	// This function performs standard Mystery authentication
	// It returns an associative array:
	//
	// $user_info['user_username'] 
	// $user_info['user_first_name']
	// $user_info['user_last_name']
	// $user_info['user_email']
	//
	// Why the username?  Because the user may enter their email address instead.
		
	global $_MYSTERY;

	$user_info = array();
	
	/* START: Custom Authentication Function */


	
	/* END: Custom Authentication Function */
		
	return $user_info;

}

function mystery_web_auth($username, $password) {

	// FIX -- This function is not yet tested
	// This function authenticates a mystery user to a web page requiring standard auth

	global $_MYSTERY;
	
	$user_info = array();
	
	/* START: Custom Authentication Function */

	// setup the server we will connect to

	$host = 'www.example.com';
	$port = '80';
	$path = '/directory/';
	$errno = '';
	$errstr = '';

	// get the web page.  If we can connect and don't get a 401 error set the user_info values
	
	$socket = @fsockopen( $host, $port, $errno, $errstr, 30 );
	
	if ($socket) {
	
		$headers = array();
	
		$headers[] = 'GET ' . $path . ' HTTP/1.0';
		$headers[] = 'Host: ' . $host;
		$headers[] = 'Authorization: ' . base64_encode('Basic: ' . $username . ':' . $password);

		fwrite ($socket, implode("\r\n". $headers) . "\r\n");

		$http_response = fgets( $socket, 22 );
		  
		if (preg_match('~200 OK~', $http_response)) {
			   
			$user_info['user_name'] = $username;
			$user_info['first_name'] = '';
			$user_info['last_name'] = $username;
			$user_info['email_address'] = '';
		
		}
		
		fclose($socket);
	
	}
	
	/* END: Custom Authentication Function */
		
	return $user_info;	

}

function mystery_guest_auth($username, $password) {

	// This function allows for a guest user without any authentication (insecure!)

	global $_MYSTERY;

	$user_info = array();
	
	/* START: Custom Authentication Function */

	$user_info['user_name'] = 'guest_user_account';
	$user_info['first_name'] = 'Guest';
	$user_info['last_name'] = 'User';
	$user_info['email_address'] = 'mystery_guest@burney.ws';
	
	/* END: Custom Authentication Function */
		
	return $user_info;	

}

?>
