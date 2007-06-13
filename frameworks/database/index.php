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

index.php

This is the main mystery application file.  It provides a general
end-user interface to the databases managed by the Mystery system.

**************************************************************/

// include the library file

include_once './mystery_library.php';

// Some actions do not require authenticated users, so...

if ($_REQUEST['action'] == 'create' || $_REQUEST['action'] == 'reset') {

	session_destroy();

	switch ($_REQUEST['action']) {
	
		case 'create':
		
			$_MYSTERY['page_title'] .= ' -> Create ' . ucfirst($_MYSTERY['word_that_means_account']);

			mystery_process_account_creation();
	
		break;
			
		case 'reset':
			
			$_MYSTERY['page_title'] .= ' -> Reset Password';

			mystery_expire_page();
			mystery_process_password_reset();
	
		break;
		
	}

	exit;

}

// Process logout if the user is logging out

if ($_REQUEST['action'] == 'logout') {

	session_destroy();
	mystery_setup_default_session();
	mystery_header();
	mystery_display_user_feedback('You have successfully logged out.');

}

// Process the authentication for the user.  If not logged in, it will display the login box

mystery_process_authentication();

// Load the configuration for this table, if applicable

if ($_REQUEST['table'] != 'none') {
	mystery_get_table_configuration($_REQUEST['table']); 
}

// FIX 
if (isset($_REQUEST['ss'])) { mystery_print_r($_SESSION); }


// Determine which action the user is looking for

switch($_REQUEST['action']) {

	case 'redirect':
		mystery_redirect($_REQUEST['location']);
	break;
	
	case 'help':
		mystery_header();
		mystery_display_help();	
		mystery_footer();
	break;
	
	case 'documentation':
		mystery_header();
		mystery_display_documentation();
		mystery_footer();
	break;
	
	case 'user_info':
		mystery_header();
		mystery_display_user_info_form();	
		mystery_footer();
	break;

	case 'user_info_submit':
		mystery_header();
		mystery_process_user_info_form();	
		mystery_footer();
	break;

	case 'error_log':
		mystery_header();
		mystery_display_error_log();	
		mystery_footer();
	break;

	case 'security_log':
		mystery_header();
		mystery_display_security_log();	
		mystery_footer();
	break;

	case 'view_data':
		mystery_header();
		mystery_display_view_data_page();
		mystery_footer();
	break;

	case 'view_record':
		mystery_header();
		mystery_display_view_record_page();
		mystery_footer();
	break;

	case 'find_data':
		mystery_header();
		mystery_display_find_data_page();
		mystery_footer();
	break;

	case 'add_data':
		mystery_header();
		mystery_display_add_data_page();
		mystery_footer();
	break;

	case 'delete_data':
		mystery_header();
		mystery_display_add_data_page();
		mystery_footer();
	break;

	case 'custom':
	
	break;
	
	case 'select-table':
	default:
	
		mystery_header();
		mystery_display_select_table_list();
		mystery_footer();

	break;

}


/*
mystery_header();
echo 'Starting...';

@trigger_error('test error', E_USER_WARNING);

mystery_print_r($_SESSION);
mystery_footer();
*/

?>
