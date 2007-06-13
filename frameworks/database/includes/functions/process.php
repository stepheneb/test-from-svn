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

functions/process.php

This is the process functions file.  These functions do most of
the real work in mystery

**************************************************************/

function mystery_process_authentication() {

	// this function processes a user's authentication, displaying login forms,
	// error messages, etc.

	global $_MYSTERY;
	
	if (@$_SESSION['is_logged_in'] == 'yes') { return; }
	
	mystery_setup_default_session();

	if (@$_REQUEST['username'] == '' || @$_REQUEST['password'] == '') {
		
		// the user didn't send a password / username, so just display the form
		mystery_header();
		mystery_display_authentication_form();
		mystery_footer();
	
	} else {
	
		// user provided some authentication information, attempt to authenticate
		
		if (!mystery_auth($_REQUEST['username'], $_REQUEST['password'])) {
		
			// user couldn't be authenticated, display error message and login box again
			mystery_header();
			mystery_display_user_error('You entered an invalid username or password, or cannot login from your current location.  Please try again.');
			mystery_display_authentication_form();
			mystery_footer();
		
		}
	
	}

}


function mystery_process_account_creation() {

	// this function processes a user's account creation, displaying forms,
	// error messages, etc.

	global $_MYSTERY;

}

function mystery_process_password_reset() {

	// this function processes a user's password reset, displaying forms,
	// error messages, etc.

	global $_MYSTERY;

}

function mystery_process_user_info_form() {

	// this function processes a user's info update form.

	global $_MYSTERY;
	
	// set elements in the data array and update the session
	
	$_SESSION['user_first_name'] = $data['user_first_name'] = $_REQUEST['user_first_name'];
	$_SESSION['user_last_name'] = $data['user_last_name'] = $_REQUEST['user_last_name'];
	$_SESSION['user_email'] = $data['user_email'] = $_REQUEST['user_email'];
	if ($_MYSTERY['allow_username_changes'] == 'yes') {
		$_SESSION['user_username'] = $data['user_username'] = $_REQUEST['user_username'];
	}
	
	// check to see if the passwords match and are set.  If not, display error and the form again
	
	if ($_REQUEST['password_one'] != '') {
		
		// user want's to change password
	
		if ($_REQUEST['password_one'] != $_REQUEST['password_two']) {
	
			mystery_display_user_error('Your passwords do not match. Please try again.');
			mystery_display_user_info_form();
			return;
		
		} else {
		
			// passwords match, add to the update data array
			$data['user_password'] = md5($_REQUEST['password_one']);
		
		}
	
	}
	
	// prepare the rest of the items for the update query
	
	$table = $_MYSTERY['table_prefix'] . 'users';
	$key = 'user_id';
	$key_value = $_SESSION['user_id'];
	
	// perform the update query
	
	if (mystery_update_query($table, $data, $key, $key_value)) { 
	
		mystery_display_user_feedback('Update Successful!');
		
		echo '
		<p>Your personal information was updated successfully.  Any username/password change
		will take effect at your next login.</p>
		
		<p><a href="' , $_SERVER['SCRIPT_NAME'] , '">Return to the Main Menu</a></p>
		';
	
	} else {

		mystery_display_user_error('Could not update Personal Information.');
		
		mystery_display_admin_contact_info();

	}

}

?>
