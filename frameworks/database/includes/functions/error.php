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

functions/error.php

This is the error function file.  It defines an error handler
to be used instead of the default PHP error handler.

**************************************************************/

function mystery_error_handler($type, $message, $file, $line, $context) {

	// This function replaces the built in PHP error handler

	global $_MYSTERY;
	
	// Check to see if this error was prepended with @
	
	if (error_reporting() == 0) { return; }
	
	$nice_types[E_NOTICE] = 'PHP Notice';
	$nice_types[E_USER_NOTICE] = 'Application Notice';
	$nice_types[E_WARNING] = 'PHP Warning';
	$nice_types[E_USER_WARNING] = 'Application Warning';
	$nice_types[E_USER_ERROR] = 'Application Fatal Error';
	if (defined('E_STRICT')) {
		$nice_types[E_STRICT] = 'PHP Code Needs Update';
	}
	if (defined('E_RECOVERABLE_ERROR')) {
		$nice_types[E_RECOVERABLE_ERROR] = 'Recoverable Application Error';
	}
	
	$now = date('Y-m-d h:i:s');
	
	$error_parts = array();
	$error_parts[] = 'Date: ' . $now;
	$error_parts[] = 'Type: ' . $nice_types[$type];
	$error_parts[] = 'Message: ' . $message;
	$error_parts[] = 'File: ' . $file;
	$error_parts[] = 'Line: ' . $line;

	$error_string = implode("\n", $error_parts) . "\n\n";
	
	if (!defined('E_STRICT') || $type != E_STRICT) {
		mystery_log_error_to_file('error_log', $error_string);
	}

	$table = $_MYSTERY['table_prefix'] . 'error_log';
	
	$data = array();
	$data['error_type'] = $nice_types[$type];
	$data['error_message'] = $message;
	$data['error_file'] = $file;
	$data['error_line'] = $line;
	$data['error_date'] = $now;
	
	if (!defined('E_STRICT') || $type != E_STRICT) {
		$this_error = mystery_insert_query($table, $data, 'error_id');
	}

	switch ($type) {
	
		case E_NOTICE:	
		case E_USER_NOTICE:
		
			if (@$_SESSION['is_administrator'] == 'yes') {
				echo '<p style="background-color: #CEFFB5;">Notice: ' , nl2br($error_string) , '</p>';
			}
		
		break;
	
		case E_WARNING:
		case E_USER_WARNING:
		
			if (@$_SESSION['is_administrator'] == 'yes') {
				echo '<p style="background-color: #FCFFB5;">Warning: ' , nl2br($error_string) , '</p>';
			}
		
		break;
		
		case E_USER_ERROR:
			
			//mystery_header();
						
			if (@$_SESSION['is_administrator'] == 'yes') {
				echo '<p style="background-color: #FFB5B5;">Fatal Error: ' , nl2br($error_string) , '</p>';
				// The following outputs way too much data. Uncomment if you must.
				// echo '<pre style="background-color: #FFB5B5;">' . print_r($context) . '</pre>';
			} else {
				echo '
				<h1>An Unexpected Error Occurred</h1>
				<p>We regret than an unexpected error has occurred.  The error has been logged
				and the administrator of the system will look into it as soon as possible.</p>
				';
				mystery_display_admin_contact_info();
			}
			
			//mystery_footer();
			exit;
		
		break;
	
	}

}

function mystery_simple_error_handler($type, $message, $file, $line, $context) {

	// This function replaces the built in PHP error handler in a very simple way to display a nice message to the user

	global $_MYSTERY;
	
	// Check to see if this error was prepended with @
	
	if (error_reporting() == 0) { return; }
	
	$nice_types[E_NOTICE] = 'PHP Notice';
	$nice_types[E_USER_NOTICE] = 'Application Notice';
	$nice_types[E_WARNING] = 'PHP Warning';
	$nice_types[E_USER_WARNING] = 'Application Warning';
	$nice_types[E_USER_ERROR] = 'Application Fatal Error';
	if (defined('E_STRICT')) {
		$nice_types[E_STRICT] = 'PHP Code Needs Update';
	}
	if (defined('E_RECOVERABLE_ERROR')) {
		$nice_types[E_RECOVERABLE_ERROR] = 'Recoverable Application Error';
	}
	
	$now = date('Y-m-d h:i:s');
	
	$error_parts = array();
	$error_parts[] = 'Date: ' . $now;
	$error_parts[] = 'Type: ' . $nice_types[$type];
	$error_parts[] = 'Message: ' . $message;
	$error_parts[] = 'File: ' . $file;
	$error_parts[] = 'Line: ' . $line;

	$error_string = implode("\n", $error_parts) . "\n\n";
	
	if (!defined('E_STRICT') || $type != E_STRICT) {
		mystery_log_error_to_file('error_log', $error_string);
	}

	switch ($type) {
	
		case E_NOTICE:	
		case E_USER_NOTICE:
		
			if (@$_SESSION['is_administrator'] == 'yes') {
				echo '<p style="background-color: #CEFFB5;">Notice: ' , nl2br($error_string) , '</p>';
			}
		
		break;
	
		case E_WARNING:
		case E_USER_WARNING:
		
			if (@$_SESSION['is_administrator'] == 'yes') {
				echo '<p style="background-color: #FCFFB5;">Warning: ' , nl2br($error_string) , '</p>';
			}
		
		break;
		
		case E_USER_ERROR:
			
			//mystery_header();
			
			if (@$_SESSION['is_administrator'] == 'yes') {
				echo '<p style="background-color: #FFB5B5;">Fatal Error: ' , nl2br($error_string) , '</p>';
				// The following outputs way too much data. Uncomment if you must.
				// echo '<pre style="background-color: #FFB5B5;">' . print_r($context) . '</pre>';
			} else {
				echo '
				<h1>An Unexpected Error Occurred</h1>
				<p>We regret than an unexpected error has occurred.  The error has been logged
				and the administrator of the system will look into it as soon as possible.</p>
				';
				mystery_display_admin_contact_info();
			}
			
			//mystery_footer();
			exit;
		
		break;
	
	}

}



?>
