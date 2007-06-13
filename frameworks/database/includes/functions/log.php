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

functions/log.php

This is the log function file.  It defines functions that log
errors and security/access violations to a log file.

**************************************************************/

function mystery_log_error_to_file($log_index, $error_string) {

	// This function logs a string to the global error file
	
	global $_MYSTERY;

	if (@$_MYSTERY[$log_index] != '') {
		
		// open a log file connection
		if ($fp = @fopen($_MYSTERY[$log_index], 'a+')) {
			fwrite($fp, $error_string);
			fclose($fp);
		}
	
	}
	
}


function mystery_log_violation($code, $message = '') {

	// This function process a serious error/violation
	
	global $_MYSTERY;
	
	$types['Red'] = 'Spoofed User';
	$types['Orange'] = 'Spoofed File';
	$types['Yellow'] = 'Spoofed Action';
	$types['Green'] = 'Illegal Query';
	$types['Blue'] = 'Virus Upload';
	$types['Purple'] = 'Spoofed Table';
	$types['Brown'] = 'Illegal Many To Many Addition';
	
	ob_start();
	echo "SERVER: ";
	print_r($_SERVER);
	echo "SESSION: ";
	print_r($_SESSION);
	echo "REQUEST: ";
	print_r($_REQUEST);
	$context = ob_get_contents();
	ob_end_clean();
	
	$table = $_MYSTERY['table_prefix'] . 'security_log';

	$data['exception_type'] = $types[$code] . ' - ' . $message; 
	$data['exception_code'] = $code; 
	$data['user_id'] = $_SESSION['user_id']; 
	$data['user_ip_address'] = $_SERVER['REMOTE_ADDR']; 
	$data['user_action'] = $_REQUEST['action'];
	$data['user_time'] = date('Y-m-d h:i:s'); 
	$data['user_request'] = $_SERVER['REQUEST_URI']; 
	$data['user_variables'] = $context;

	$log_id = mystery_insert_query($table, $data, 'record_id');

	// Prepare error string
	
	$error_parts = array();
	while (list($key, $value) = each($data)) {
		$error_parts[] .= ucwords(str_replace('_',' ',$key)) . ': ' . $value;
	}
	$error_string = implode("\n", $error_parts) . "\n\n";
	
	mystery_log_error_to_file('security_log', $error_string);
	
	// make them wait a couple seconds so they won't automate the attack
	sleep(2); 

	mystery_header();
	
	echo '
	<h1>Access Denied</h1>

	<p>Sorry, but the account you arelogged in as cannot perform the requested action. (<em>Code: ' , $code, '</em>)</p>
	';
	
	mystery_display_admin_contact_info();
	
	if ($code == 'Blue') {
	
		echo '<p>The file you tried to upload is infected with a <strong>virus</strong>.
		Please <strong>disinfect the file</strong> and try again.</p>
		<p><code>' , $_MYSTERY['virus_feedback'] , '</code></p>';

	}
	
	mystery_footer();

}

?>
