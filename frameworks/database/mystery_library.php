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

mystery_library.php

This is the main mystery library file.  Include this file in
your programs and call the functions on your page as described in
the documentation.

**************************************************************/


/**************************************************************
   ______               ____ _
  / ____/____   ____   / __/(_)____ _
 / /    / __ \ / __ \ / /_ / // __ `/
/ /___ / /_/ // / / // __// // /_/ /
\____/ \____//_/ /_//_/  /_/ \__, /
                            /____/

**************************************************************/

// Please set the following configuration options.  These are
// the necessary options before even connecting to the database.
// Other options can be set through the online configuration.

// Initialize the main array and some sub elements

$_MYSTERY = array(); // <-- DO NOT CHANGE/REMOVE

// Please define the database connection string below.
// The format of the string is:
//     protocol://username:password@hostname/database

#$_MYSTERY['db_connect_string'] = 'mysql://lhh_admin:h31pU^@localhost/mystery4b';
$_MYSTERY['db_connect_string'] = $portal_config['mystery_database_connection'];

// This is the prefix for the tables in the databse.  The default is mystery_.
// It can be overridden during installation, and then that value would need
// to be placed here insted.

$_MYSTERY['table_prefix'] = 'mystery_';

// Whether or not to use persistent connections for the database.  On a shared
// server, it should be set to false to avoid database overloading problems.  It
// can be set to true on a dedicated server for performance increases.

$_MYSTERY['use_persistent_connections'] = false;

// The file that will be used for logging errors.  If set to an empty string '',
// errors will not be logged to a file.

$_MYSTERY['error_log'] = $portal_config['error_log'];
$_MYSTERY['security_log'] = $portal_config['security_log'];

// There are several functions in the functions/authentication.php file which
// allow Mystery to use external authentication instead of it's own internal one.
// Uncomment one or more below to enable them.  They will be processed in the order
// they are listed, with the internal authentication always being processed first

#$_MYSTERY['external_auth_functions'][] = 'mystery_web_auth';
#$_MYSTERY['external_auth_functions'][] = 'mystery_guest_auth';

// Below you can set how long in seconds a default Mystery session will last

$_MYSTERY['session_life'] = 2*3600;

// You can set a different domain than the default by setting the following

#$_MYSTERY['cookie_domain'] = '.example.com';
$_MYSTERY['cookie_domain'] = $portal_config['cookie_domain'];


/**************************************************************
   _____        __
  / ___/ ___   / /_ __  __ ____
  \__ \ / _ \ / __// / / // __ \
 ___/ //  __// /_ / /_/ // /_/ /
/____/ \___/ \__/ \__,_// .___/
                       /_/

**************************************************************/

// Determine where this main library file is so we can do includes

$_MYSTERY['file_system_location'] = dirname(__FILE__);
$_MYSTERY['web_location'] = preg_replace('~^' . $_SERVER['DOCUMENT_ROOT'] . '~', '', $_MYSTERY['file_system_location']);

// Prepend the MySTRI includes location to the main include path.
// To use the existing DB or PEAR installs, change the includes below to full paths
// or remove the first directory in the $new_include_path

$current_includes_path = ini_get('include_path');
$new_include_path = 
	$_MYSTERY['file_system_location'] . '/includes/external' .$pathdivider. 
	$_MYSTERY['file_system_location'] . '/includes' .$pathdivider. 
	$_MYSTERY['file_system_location'] .$pathdivider.  
	$current_includes_path;

ini_set('include_path',$new_include_path);

ini_set('session.gc_maxlifetime', $_MYSTERY['session_life']);

if (isset($_MYSTERY['cookie_domain'])) {
	ini_set('session.cookie_domain', $_MYSTERY['cookie_domain']);
}



/**************************************************************
    ____              __            __
   /  _/____   _____ / /__  __ ____/ /___   _____
   / / / __ \ / ___// // / / // __  // _ \ / ___/
 _/ / / / / // /__ / // /_/ // /_/ //  __/(__  )
/___//_/ /_/ \___//_/ \__,_/ \__,_/ \___//____/

**************************************************************/

// Include external libraries
require_once 'PEAR.php';
require_once 'DB.php';

// Include Mystery function libraries
include_once 'functions/authentication.php';
include_once 'functions/database.php';
include_once 'functions/debug.php';
include_once 'functions/display.php';
include_once 'functions/error.php';
include_once 'functions/general.php';
include_once 'functions/generate.php';
include_once 'functions/get.php';
include_once 'functions/initialize.php';
include_once 'functions/log.php';
include_once 'functions/process.php';
include_once 'functions/session.php';




/**************************************************************
    ___                   __ _               __   _
   /   |   ____   ____   / /(_)_____ ____ _ / /_ (_)____   ____
  / /| |  / __ \ / __ \ / // // ___// __ `// __// // __ \ / __ \
 / ___ | / /_/ // /_/ // // // /__ / /_/ // /_ / // /_/ // / / /
/_/  |_|/ .___// .___//_//_/ \___/ \__,_/ \__//_/ \____//_/ /_/
       /_/    /_/

**************************************************************/

// initialize the application

mystery_initialize();

// override some initializations

$_MYSTERY['administrator_email'] = 'webmaster@concord.org';

// set up the mystery database connection

// work around to make sure if there is an auth problem it gets displayed and fixed
ini_set('error_reporting',E_ALL);

set_error_handler('mystery_simple_error_handler');

// at this point, we don't really need to connect to the database
// mystery_db_connect();

// use our custom session handlers instead of the PHP defaults

session_set_save_handler(
	'mystery_session_open',	
	'mystery_session_close',
	'mystery_session_read',
	'mystery_session_write',
	'mystery_session_destroy',
	'mystery_session_gc'
	);

// start the session

session_name($portal_config['session_name']);
session_start();

// allow the users to use the back button
header('Cache-control: private');

// use our custom error handler instead of the PHP default

set_error_handler('mystery_error_handler');

// catch all possible errors

ini_set('error_reporting',E_ALL);

// start the timer

mystery_time_results('start');

// configure the application

if (!mystery_configure()) {

	if (mystery_check_installation_status()) {
	
		mystery_header();
		mystery_display_user_error('Configuration Problem');
		echo '
		<p>Could not load the main system configuration.  The system
		Administrator should verify that the system is correctly
		installed and configured.</p>
		';
		mystery_footer();
	
	} else {
	
		mystery_header();
		mystery_display_installation_options();
		mystery_footer();
	
	}

}

?>
