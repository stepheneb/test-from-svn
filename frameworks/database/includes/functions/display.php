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

functions/display.php

This is the display functions file.  The functions listed in
this file display items to the screen.

**************************************************************/


function mystery_header() {

	global $_MYSTERY;

	if ($_MYSTERY['header_included'] != 'yes') {
	
		$_MYSTERY['header_included'] = 'yes';
		include $_MYSTERY['header_file'];
	
		mystery_display_options_menu();
		
	}
	
}

function mystery_footer() {

	global $_MYSTERY;
	
	if ($_MYSTERY['footer_included'] != 'yes') {
	
		// stop the timer
		mystery_time_results('stop');
		
		// calculate time and show results
		if ($_MYSTERY['calculate_page_time'] == 'yes') {
			mystery_time_results('display');
		}
	
		// include the footer file and exit
		$_MYSTERY['footer_included'] = 'yes';
		include $_MYSTERY['footer_file'];
		exit;
	
	}
	
}


function mystery_display_admin_contact_info() {

	global $_MYSTERY;

	echo '
	<p>You may wish to use the <strong>back button</strong> on
	your browser and try again.  You may also wish to 
	<a href="mailto:' . $_MYSTERY['administrator_email'] . '">send a message to the
	administrator.</a></p>
	';

}


function mystery_display_options_menu() {

	// This function displays the options menu and logged in user

	global $_MYSTERY;
	
	echo '
	<hr>

	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	';
	
	if ($_MYSTERY['give_mystery_credit'] == 'yes') {

		echo '<td align="left"><a href="' , $_MYSTERY['web_location'] , '"><img src="' , $_MYSTERY['web_location'] , '/images/mystery_wide_100.gif" alt="Mystery 4" width="193" height="50" border="0" align="middle"></a></td>';

	}
	
	echo '
	<td align="center">
	';
	
	// Generate Quick Nav box if logged in
	if (@$_SESSION['is_logged_in'] == 'yes') {
		echo mystery_generate_quick_nav();
	}
	
	echo mystery_generate_options_menu() , '
	</td>
	<td align="right"><small>Currently logged in as:<br><strong>' , @$_SESSION['user_first_name'] , '&nbsp;' , @$_SESSION['user_last_name'] , '</strong></small></td>
	</tr>
	</table>
	
	<hr>
	
	';

}


function mystery_display_select_table_list() {

	// this function generates and displays the table list for the main menu page
	
	global $_MYSTERY;
	
	$table_list_parts = array();
	
	$menu_items = mystery_get_main_menu_items();
	
	reset($menu_items);
	
	$total = count($menu_items['name']);
	$half = ceil($total/2);
	
	$current_prefix = '';
	$list_count = 0;
	$this_split = 'no';

	while (list($key, $value) = each($menu_items['name'])) {
	
		$this_prefix = substr($value, 0, strpos($value, ':'));
		
		$this_separator = '';
		
		if (($this_prefix != $current_prefix) && ($list_count > 0)) {
			$this_separator .= '</ul>';
			if ($list_count >= $half && $this_split == 'no') {
				$this_separator .= '
				</td><td width="50%" valign="top" align="left">
				';
				$this_split = 'yes';
			}
			$this_separator .= '<ul>';
		}

		$this_link = $_SERVER['SCRIPT_NAME'] . '?action=' . $menu_items['action'][$key] . '&amp;table=' . $menu_items['table'][$key];
	
		$table_list_parts[] = '
		' . $this_separator . '
		<li>
			<a href="' . $this_link . '" title="' . htmlspecialchars($menu_items['description'][$key]) . '" class="MainMenuList">' . htmlspecialchars($value) . '</a>
			<a href="' . $this_link . '" title="Open in a new window">' . $_MYSTERY['new_window_image'] . '</a>
		</li>
		';
	
		$current_prefix = $this_prefix;
		$list_count++;
		
	}
		
	echo '
	<h1>Main Menu</h1>
	
	<p>Please choose a ' , $_MYSTERY['word_that_means_table'] , ' from the list
	below:</p>
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><td width="50%" valign="top" align="left">
	
	<ul>
	' , implode("\n", $table_list_parts) , '
	</ul>

	</td></tr>
	</table>
	';

}

function mystery_display_user_error($message) {

	// this function displays a nice error to the user
	
	global $_MYSTERY;
	
	echo '
	<div class="UserError">
	
	<p><img src="' . $_MYSTERY['web_location'] . '/images/exclamation.png" width="23" height="40" alt="Error!" align="middle"> ' , $message , '<br clear="left"></p>
	
	</div>
	';

}


function mystery_display_user_feedback($message) {

	// this function displays a nice error to the user
	
	global $_MYSTERY;
	
	echo '
	<div class="UserFeedback">
	
	<p><img src="' . $_MYSTERY['web_location'] . '/images/green-splat.png" width="34" height="40" alt="Message: " align="middle"> ' , $message , '<br clear="left"></p>
	
	</div>
	';

}


function mystery_display_authentication_form($action = '') {

	// this function displays an authentication form to the user
	
	global $_MYSTERY;
	
	if ($action == '') {
	
		if (strpos($_SERVER['REQUEST_URI'], 'logout')) {
			
			// just use the script name because this was the logout
			$action = $_SERVER['SCRIPT_NAME'];

		} else {
	
			// This lets the user click on a link and login immediately
			$action = str_replace('&','&amp;', $_SERVER['REQUEST_URI']);
		
		}
	
	}
	
	$email_option = '';
	
	if ($_MYSTERY['allow_email_authentication'] == 'yes') {
		$email_option = ' (or E-mail address)';
	}
	
	echo '
	<div id="authentication_form">
	
	<form action="' , $action , '" method="post">
	
	<p><strong>Please login below:</strong></p>
	
	<p>Username' , $email_option , ':<br>
	<input type="text" id="username" name="username" value="' , @$_REQUEST['username'] , '" size="30"></p>
	
	<p>Password:</p>
	<input type="password" name="password" value="" size="30">
	
	<p><input type="submit" value="Login"></p>
	
	</form>

	</div>
	
	';
	
	if (@$_MYSTERY['enable_javascript'] == 'yes') {
		
		// focus the username field

		echo '
		<script type="text/javascript" language="javascript">
			document.getElementById(\'username\').focus();
		</script>
		';
	
	}

}


function mystery_display_account_creation_form() {

	echo 'account form';

}

function mystery_display_password_reset_form() {

	echo 'password form';

}

function mystery_display_user_info_form() {

	// This function displays a form to the user that allows them to change their
	// login and other personal info

	global $_MYSTERY;

	echo '
	<h1>Update your Personal Information</h1>
	
	<form action="' . $_SERVER['SCRIPT_NAME'] . '" method="post">
	
	<table cellspacing="0" cellpadding="2" border="1">
	
	<tr class="TableDarkCell">
	<td valign="top"><strong>User Name</strong></td>
	<td valign="top">
	';
	
	if ($_MYSTERY['allow_username_changes'] == 'yes') {
		echo '<input type="text" name="user_username" value="' , $_SESSION['user_username'] , '" size="30">';
	} else {
		echo '<strong>' , $_SESSION['user_username'] , '</strong>';
	}
	
	echo '
	</td>
	</tr>
	
	<tr class="TableLightCell">
	<td valign="top"><strong>E-Mail Address</strong></td>
	<td valign="top"><input type="text" name="user_email" value="' , $_SESSION['user_email'] , '" size="30"></td>
	</tr>
	
	<tr class="TableDarkCell">
	<td valign="top"><strong>New Password</strong><br>
	<small>(blank for no change)</small></td>
	<td valign="top"><input type="password" name="password_one" value="" size="30"></td>
	</tr>

	<tr class="TableLightCell">
	<td valign="top"><strong>Repeat Password</strong></td>
	<td valign="top"><input type="password" name="password_two" value="" size="30"></td>
	</tr>
	
	<tr class="TableDarkCell">
	<td valign="top"><strong>First Name (Given Name)</strong></td>
	<td valign="top"><input type="text" name="user_first_name" value="' , $_SESSION['user_first_name'] , '" size="30"></td>
	</tr>
	
	<tr class="TableLightCell">
	<td valign="top"><strong>Last Name (Surname / Family Name)</strong></td>
	<td valign="top"><input type="text" name="user_last_name" value="' , $_SESSION['user_last_name'] , '" size="30"></td>
	</tr>
	
	</table>
	
	<p>
	<input type="hidden" name="action" value="user_info_submit">
	<input type="submit" value="Update Personal Info">
	<input type="hidden" name="table" value="' , $_REQUEST['table'] , '"> 
	';
	
	if ($_MYSTERY['enable_javascript'] == 'yes'){
		echo '<input type="button" value="Cancel" onclick="history.back();">';
	}
	
	echo '
	</p>
	</form>
	';

}

function mystery_display_help() {

	// This function displays the user portion of the documentation
	
	global $_MYSTERY;

	// read in the file
	$this_file = implode('',file($_MYSTERY['file_system_location'] . '/docs/documentation.html'));

	// parse out the user guide section
	echo preg_replace('~.*?<!-- Start User Section -->(.*)<!-- End User Section -->.*~is', '$1', $this_file);

}

function mystery_display_documentation() {

	// This function displays the documentation page to an admin user
	
	global $_MYSTERY;

	if ($_SESSION['is_administrator'] == 'yes') {
	
		// read in the file
		$this_file = implode('',file($_MYSTERY['file_system_location'] . '/docs/documentation.html'));
	
		// get rid of the html header...
		echo preg_replace('~.*?<body[^>]*>(.*)</body.*~is','$1',$this_file);
	
	} else {
	
		// The user is not an administrator, show error page and exit
		mystery_log_violation('Red', 'Non-admin attempt to look at full documentation');
	
	}

}

function mystery_display_error_log() {

	global $_MYSTERY;

	echo '
	<h1>Error Log</h1>
	';

}

function mystery_display_security_log() {

	global $_MYSTERY;

	echo '
	<h1>Security Log</h1>
	';

}

function mystery_display_view_record_page() {

	global $_MYSTERY;


}

function mystery_display_view_data_page() {

	global $_MYSTERY;
		
	// shortcut to make life easier
	
	$t = &$_MYSTERY['table_info'][$_REQUEST['table']];
	
	//mystery_print_r($t);

	echo '<h1>Data from ' , $t['display_name'] , '</h1>';
	
	$query_string = @$_REQUEST['query_string'];

	if ($query_string == '') { 
	
		// if the admin added a semicolon at the end, strip it
		$query_string = preg_replace('~;\s*?$~','', $t['default_query']);
		
		// if the admin didn't enter a default query, make the simple select *
		if ($query_string == '') {	
			$query_string = 'SELECT * FROM ' . $t['real_name'];
		}
		
	}
	
	// Make sure that this query is displayable (i.e., contains SELECT)
	
	if (!preg_match('~^select ~i', $query_string) || preg_match('~into (outfile|dumpfile)~i', $query_string)) {
		mystery_log_violation('Green', 'View data query contained an outfile phrase or did not begin with select - ' . $query_string);
	}
	
	// Store the value of the $query_string variable without any order_by clauses
	
	$prev_query_string = $query_string;
	
	// Check for foreign keys and add data to associative array that can be referenced
	// by the field values later in the script
	
	if (count($t['foreign_keys'] > 0)) {
		
		reset($t['foreign_keys']);
	
		$fk_field_display = array();
		
		for ($i = 0; $i < count($t['foreign_keys']); $i++) {
			
			while (list($eds_key, $eds_value) = each($t['foreign_keys'])) {
	
				$query = 'SELECT DISTINCT ' . mystery_convert_csv_to_concat($t['foreign_keys'][$eds_key]['label'])  . ' AS fk_label, ' . $foreign_keys[$eds_key]['value'] . ' AS fk_value FROM ' . $foreign_keys[$eds_key]['table'] . ' ORDER BY fk_label';
				$params = array();
				
				$result = mystery_select_query($query, $params);
				for ($i = 0; $i < count($result); $i++) {
					$fk_field_display[$eds_key][$result[$i]['fk_value']] = htmlspecialchars($result[$i]['fk_label']);
				}
	
			}
	
		}
				
	}
	
	// If the user is a row access user, only grab her rows
	
	//PAUL, START HERE>>>>
	if (($this_access_type == 'row') && ($this_table_owner_key != '')) {
		
		$glue_word = ' WHERE ';
		if (preg_match('~ where ~i', $query_string)) { $glue_word = ' AND '; }	
		$user_term = $this_table_owner_key . '="' . $this_owner_id . '"';
		$where_clause = $glue_word . $user_term;
		if (!preg_match("~$user_term~i",$query_string)) {
			$query_string .= $where_clause;
			$glue_word = ' AND ';
		}
		
	}
	
	// Set the field to sort the results by and the direction
	
	
	if (($order_by == '')) {
	
		$order_by = $this_table_default_order_field;
		
	}
	
	
	
	if ($reverse_sort == '') { $reverse_sort = $this_table_default_reverse_sort; }
	
	if ($reverse_sort == 'yes') { $desc = ' DESC'; } else { $desc = ''; }
	
	if ($order_by != '') {
	
		$query_string .=  ' ORDER BY ' . $order_by . $desc;
		
	}
	
	if ($in_admin_group == 'yes') {
	
		echo '<p><small>' , $query_string , '</small></p>';
	
	}
	
	// Perform the query
	$result = mysql_query($query_string, $dbh);
	$error_message = mysql_error();
	
	
	// Show an error if one occurs now
	
	if ($error_message != '') {
		$error_message = mysql_errno() . ': ' . $error_message;
		echo '<p><span class="error">ERROR: ' , $error_message , '</span></p>';
	}
	
	
	


}

function mystery_display_find_data_page() {

	global $_MYSTERY;


}

function mystery_display_add_data_page() {

	global $_MYSTERY;


}

function mystery_display_delete_data_page() {

	global $_MYSTERY;


}

function mystery_display_installation_options() {

	global $_MYSTERY;

	echo '
	<h1>Mystery System Installation</h1>
	';

}


?>
