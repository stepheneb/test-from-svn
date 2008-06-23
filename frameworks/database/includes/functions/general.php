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

functions/general.php

This is the general function file.  Most non-specialized Mystery
functions are located in this file.

**************************************************************/


function mystery_print_r() {

	// This function is a nicer version of the standard print_r function.  It supports
	// multiple arguements and displays preformatted in html with a distinct background
	// if you pass a parameter beginning with a #, it will be used as the new background
	// color other than the default royal blue (#000099)

	$numargs = func_num_args();
	$arg_list = func_get_args();

	$bg = '#000099';

	ob_start();
	for ($i = 0; $i < $numargs; $i++) {
		if (@$arg_list[$i][0] == '#') {
			$bg = $arg_list[$i];
		} else {
			print_r($arg_list[$i]);
		}
	}
	$output = ob_get_contents();
	ob_end_clean();
	
	echo '<pre style="margin: 2em; padding: 2em; border: 2 dotted; background-color: ' . $bg . '; color: #ffffff;">' , htmlspecialchars($output) , '</pre>';


}


function mystery_configure() {

	// This function gets the configuration from the databse and stores
	// it in the $_MYSTERY variable

	global $_MYSTERY;
	
	$query = 'SELECT directive, value FROM ' . $_MYSTERY['table_prefix'] . 'configuration';
	
	$params = array();
	
	$config_variables = mystery_select_query($query, $params);
	
	for ($i = 0; $i < count($config_variables); $i++) {
	
		$_MYSTERY[$config_variables[$i]['directive']] = $config_variables[$i]['value'];
	
	}
	
	return $i;
	
}

function mystery_time_results($command) {

	// This function times a request to the Mystery system

	global $_MYSTERY;
	
	switch ($command) {
	
		case 'start':
			$_MYSTERY['t0'] = microtime();
		break;
		
		case 'stop':
			$_MYSTERY['tf'] = microtime();
		break;
		
		case 'display':
	
			$t0_parts = explode(' ', $_MYSTERY['t0']);
			$t_parts = explode(' ', $_MYSTERY['tf']);
			
			$seconds_part = $t_parts[1] - $t0_parts[1];
			$microseconds_part = $t_parts[0] - $t0_parts[0];
		
			echo '
			<hr>
			<p align="center"><small>Generated
			';
					
			if ($_MYSTERY['give_mystery_credit'] == 'yes') {
				echo 'by <a href="http://www.burney.ws/software/mystery/" target="_blank" title="Visit the Mystery Home Page">Mystery 4.0.0</a>';
			}
			
			echo '
			in <strong>' , (round(($seconds_part + $microseconds_part),3)) , '</strong> seconds.</small></p>
			<hr>
			';
	
		break;
		
	}

}

function mystery_redirect($location) {

	// This is a universal redirect function that will attempt to make a redirect work
	// with browsers that don't understand 301/302 response headers (rare)
	
	if (!preg_match('~^http://~', $location)) {
		$location = 'http://' . $_SERVER['HTTP_HOST'] . $location;
	}

	@header("Location: $location");

	echo '
	<html>
	<head>
		<title>Loading...</title>
		<meta http-equiv="refresh" content="0; URL=' , $location , '">
	</head>

	<body bgcolor="#ffffff" onload="location.href=\'' , $location , '\'">
		<p><a href="' , $location , '">Follow this link to continue</a></p>
	</body>

	</html>
	';

	exit;

}


function mystery_cookie($name, $value, $lifetime = '', $path='/', $domain = '', $secure = 0) {

	// this function creates a cookie for the user;  
	// if value is set to '', the cookie is deleted
	// lifetime is a time in seconds
	
	// in the future, we may wish to roll our own cookies based on the RFC:
	// http://www.ietf.org/rfc/rfc2109.txt, something like
	// Set-Cookie: Name=Value; Max-Age=Secs; Domain=.www.domain.com; Path=/; Secure=1;\n\n
	// 1 hour cookie is as follows:
	// header('Set-Cookie: TestCookie=something+from+somewhere; Max-Age=3600; Domain=.www.domain.com; Path=/; secure;');
	// Delete a cookie, set -1:
	// header('Set-Cookie: TestCookie=something+from+somewhere; Max-Age=-1; Domain=.www.domain.com; Path=/; secure;');
	// That helps cope with computers that have their dates set incorrectly
	
	global $_MYSTERY;
	
	if ($domain == '') {
		$domain = $_MYSTERY['cookie_domain'];
	}

	if ($value == '') {
		$expires = mktime(6,20,0,8,9,1995);
		setcookie($name, $value, $expires, $path, $domain, $secure);
	} else {
		if ($lifetime == '') {
			$expires = false;
		} else {
			$expires = time() + $lifetime;
		}
		setcookie($name, $value, $expires, $path, $domain, $secure);
	}

}

function mystery_create_token() {

	// This function creates a user_token used for newly created accounts and
	// password resets

    list($usec, $sec) = explode(' ', microtime());
    $seed = (float) $sec + ((float) $usec * 100000);

	mt_srand($seed);

	return md5(uniqid('',1));

}

function mystery_send_email($to,$from,$subject,$body,$message_id='',$cc=array(),$bcc=array(), $reply_to = '') {
 
	// This function sends an email with the appropriate headers
	// We can mess with the $eol for different email servers/platforms if necessary
	
	if ($message_id = '') { $message_id = '<Mystery-' . md5(uniqid('')) . '@' . $_SERVER['HTTP_HOST'] . '>'; }
	
	$strip_to = preg_replace('~.*?<(.*?)>~', '\1', $to);
	$strip_from = preg_replace('~.*?<(.*?)>~', '\1', $from);
	
	// this is the proper $eol for all email
	$eol = "\r\n";
	
	// Do some "special" things for windows
	if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
		$to = $strip_to;
		//$eol = "\r\n";
	} else {
		//$eol = "\n";
	}
	
	// PB: this code is not yet ready for prime time
	// fix the to and from addresses if necessary
	//
	//if ($to != $strip_to) {
	//	$to = preg_replace('(.*?)<(.*?)>', '"\1"<\2>', addslashes($to));
	//}
	
	$headers = array();
	
	$headers[] = "Return-Path: <" . $strip_from . ">";
	$headers[] = "Bounce-To: " . $strip_from;
	$headers[] = "From: " . $from;
	//$headers[] = "To: " . $to;
	
	if (count($cc) > 0) {
		$headers[] = "Cc: " . implode(', ', $cc);
	}

	if (count($bcc) > 0) {
		$headers[] = "Bcc: " . implode(', ', $bcc);
	}
	
	if ($reply_to != '') {
		$headers[] = "Reply-To: " . $reply_to;
	} else {
		$headers[] = "Reply-To: " . $from;
	}
	
	$headers[] = "Message-Id: " . $message_id;
	$headers[] = 'Content-type: text/plain; charset="utf-8"';
	$headers[] = "X-Mailer: PHP/" . phpversion();
	$headers[] = "X-Mail-Origin: " . @$_SERVER['HTTP_HOST'] . " web to email system";
	$headers[] = "X-Sender-Network-Address: " . @$_SERVER['REMOTE_ADDR'];
	$headers[] = "X-Disclaimer: This is an automated message from a system at " . @$_SERVER['HTTP_HOST'] . ".";
	$headers[] = "X-Apparent-Source-Page: http://" . @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'];
	$headers[] = "X-Site-Maintainer: support@lotsahelpinghands.com";
	
	$header_string = implode($eol, $headers);
	
	$cmd_param = ' -f' . $strip_from;
	
	//$body = wordwrap($body, 76, $eol);
	$body = preg_replace("~\r\n|\r|\n~", $eol, $body);
	
	if (!mail($to, $subject, $body, $header_string, $cmd_param)) { 
		trigger_error('The email to ' . $to . ' from ' . $from . ' was not sent.', E_USER_WARNING);
		return 0; 
	} else { 
		return 1; 
	}

}

function mystery_encode_email($message, $type = '') {

	// this function encodes an email in one of various formats
	// for typical email, we can just follow RFC 2822 and not encode
	// the email since the line length is typically less than the max 
	// of 998 characters.  This function will be necessary
	// for possible future HTML encoded messages.

	switch($type) {
	
		case 'quoted/printable':
		
			$message = preg_replace( '/[^\x21-\x3C\x3E-\x7E\x09\x20]/e', 'sprintf( "=%02x", ord ( "$0" ) ) ;',  $message );
			
			preg_match_all( '/.{1,73}([^=]{0,3})?/', $message, $matches );
			
			return implode( "=\r\n", $matches[0] );
			
		break;
		
		default:
		
			return $message;
		
		break;
	
	}
	
}

function mystery_expire_page() {

	// this function sends headers to the browser to make the page, hopefully, un-cacheable

	header("Expires: Wed, 9 Aug 1995 11:20:00 GMT");    // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); // HTTP/1.0

}

function mystery_convert_results_to_simple_array($results, $key_field) {

	$lookup = array();
	
	for ($i = 0; $i < count($results); $i++) {
		
		$lookup[] = $results[$i][$key_field];
	
	}
	
	return $lookup;

}

function mystery_convert_results_to_lookup_array($results, $key_field, $value_field) {

	$lookup = array();
	
	for ($i = 0; $i < count($results); $i++) {
	
		if ($results[$i][$key_field] == '') {
			$results[$i][$key_field] = 'null-key';
		}
	
		$lookup[$results[$i][$key_field]] = $results[$i][$value_field]; 
	
	}
	
	return $lookup;

}

function mystery_convert_results_to_full_lookup_array($results, $key_field) {

	$lookup = array();
	
	for ($i = 0; $i < count($results); $i++) {
	
		if ($results[$i][$key_field] == '') {
			$results[$i][$key_field] = 'null-key';
		}
	
		$lookup[strtolower($results[$i][$key_field])] = $results[$i]; 
	
	}
	
	return $lookup;

}



?>
