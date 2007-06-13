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

functions/authentication.php

This is the authentication function file.  It contains the
functions used for authentication.  If you don't want to use the
default authentication methods, you can define your own in the
custom/authentication.php file, then specify the function names
in the main mystery_library.php file.

**************************************************************/

function mystery_auth($username, $password) {

	// This is the general wrapper function for mystery authentication
	// It takes a username and password and attempts to authenticate
	// to the mystery_users table.  
	//
	// If successful, it sets $_SESSION
	// variables for username, full name, and last name
	// and returns true;
	//
	// If it fails, it tries any of the configured external
	// authentication sources.  If those are successful, it
	// checks for extant mystery records for that user, creating
	// them if they don't exist.  If they fail it continues to the
	// next source until exhausted.
	//
	// After all else fails, if the user still can't be authenticated
	// the function takes a brief pause then returns false

	global $_MYSTERY;
	
	// Don't authenticate if user is logged in
	
	if (@$_SESSION['is_logged_in'] == 'yes') { return true; }

	// Try internal Mystery authentication first
		
	$user_info = mystery_internal_auth($username, $password);

	if (count($user_info) == 0) {

		if (isset($_MYSTERY['external_auth_functions'])) {
		
			// some external authentication functions are set, so include the custom auth file
			
			include 'custom/authentication.php';
			
			// loop through the custom function until one hopefully works
		
			for ($i = 0; $i < count($_MYSTERY['external_auth_functions']); $i++) {
	
				// call each function with the username and password parameters
				// if we get back a non-zero array, stop checking
	
				$user_info = call_user_func($_MYSTERY['external_auth_functions'][$i], $username, $password);
				
				if (count($user_info) > 0) { break; }
	
			}

		}
		
	}
	
	if (count($user_info) == 0) {
	
		// the authentication was not successful
		//sleep(2);
		return false;

	} else {
	
		// set the user's session information
		
		$_SESSION['user_username'] = $username;
		$_SESSION['user_first_name'] = $user_info['user_first_name'];
		$_SESSION['user_last_name'] = $user_info['user_last_name'];
		$_SESSION['user_email'] = $user_info['user_email'];
		
		// check that the user is in Mystery.  If not, add them.  If so, get their id.
		
		$query = 'SELECT * FROM ' . $_MYSTERY['table_prefix'] . 'users WHERE user_username = ? OR user_email = ?';
		$params = array($_SESSION['user_username'], $_SESSION['user_username']);

		$results = mystery_select_query($query, $params);
	
		if (count($results) > 0) {
		
			// user exists, set the session user_id variable
		
			$_SESSION['user_id'] = $results[0]['user_id'];
		
		} else {
		
			// user doesn't exist.  Add them
			
			$table = $_MYSTERY['table_prefix'] . 'users';
			
			$now = date('Y-m-d h:i:s');

			$data['user_username'] = $user_info['user_username'];
			$data['user_first_name'] = $user_info['user_first_name'];
			$data['user_last_name'] = $user_info['user_last_name'];
			$data['user_email'] = $user_info['user_email'];
			$data['user_record_updated'] = $now;
			$data['user_creation_date'] = $now;
			
			$_SESSION['user_id'] = mystery_insert_query($table, $data, 'user_id');
			
			// add the user to the default groups
			
			if ($_MYSTERY['default_user_groups'] != '') {
				
				// remove whitespace and split on the commas
				$groups = explode(',', preg_replace('~\s~','',$_MYSTERY['default_user_groups']));
				
				$table = $_MYSTERY['table_prefix'] . 'users_groups';
				
				for ($i = 0; $i < count($groups); $i++) {
				
					// for each group, add a record for the user
					// NOTE: there should be a mystery function that better uses the prepared
					// statements to insert multiple values, but I'm not sure how that works
					// with sequences
					
					$data = array();
					$data['user_id'] = $_SESSION['user_id'];
					$data['group_id'] = $groups[$i];
					
					$ugid = mystery_insert_query($table, $data, 'ug_id');
				
				}
			
			}
		
		}
	
		// get the users groups and permissions
		
		$query = 'SELECT group_id FROM ' . $_MYSTERY['table_prefix'] . 'users_groups WHERE user_id = ?';
		$params = array($_SESSION['user_id']);
		
		$user_groups = mystery_select_query($query, $params);
		
		for ($i = 0; $i < count($user_groups); $i++) {
			$_SESSION['user_groups'][] = $user_groups[$i]['group_id'];
			if ($user_groups[$i]['group_id'] == '1') {
				// user is in the admin group
				$_SESSION['is_administrator'] = 'yes';
			}
		}

		// set final session varialbes
		
		$_SESSION['is_logged_in'] = 'yes';
	
		return true;
	
	}

}


function mystery_internal_auth($username, $password) {

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
	
	$query = 'SELECT * FROM ' . $_MYSTERY['table_prefix'] . 'users WHERE (user_username = ? OR user_email = ?) AND user_password = ?';
	$params = array($username, $username, md5($password));

	$results = mystery_select_query($query, $params);
	
	if (count($results) > 0) {
	
		// check this users ip address restriction as well
		
		if ($results[0]['user_valid_ip'] == '*') {
			// make it a proper regular expression
			$results[0]['user_valid_ip'] = '.*';
		}
		
		if (preg_match('~' . $results[0]['user_valid_ip'] . '~', $_SERVER['REMOTE_ADDR'])) {
	
			// user authenticates and matches the ip restriction.  Set their user info.
			
			$user_info['user_username'] = $results[0]['user_username'];
			$user_info['user_first_name'] = $results[0]['user_first_name'];
			$user_info['user_last_name'] = $results[0]['user_last_name'];
			$user_info['user_email'] = $results[0]['user_email'];
			
			// set a flag so we know they used mystery to login
			
			$_SESSION['mystery_login'] = 'yes';
		
		}
	
	}
	
	return $user_info;

}


?>
