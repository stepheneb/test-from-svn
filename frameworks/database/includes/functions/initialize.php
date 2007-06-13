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

functions/initialize.php

This very large function initializes the Mystery environment as
well as the SESSION variables.

**************************************************************/

function mystery_initialize() {

	// This function sets all $_MYSTERY parameters to their defaults
	
	global $_MYSTERY;
	
	// setup internal $_MYSTERY varialbles
	
	$_MYSTERY['dbh'] = 'not connected'; // the primary mystery connection

	$_MYSTERY['header_file'] = 'header.php';
	$_MYSTERY['footer_file'] = 'footer.php';
	
	$_MYSTERY['header_included'] = 'no';
	$_MYSTERY['footer_included'] = 'no';

	$_MYSTERY['word_that_means_table'] = 'section';

	$_MYSTERY['page_title'] = 'Mystery 4';

	$_MYSTERY['calculate_page_time'] = 'yes';

	$_MYSTERY['this_username'] = 'Not Logged In';

	$_MYSTERY['new_window_image'] = '<img src="' . $_MYSTERY['web_location'] . '/images/new_window.gif" align="middle" width="16" height="13" border="0" vspace="0" hspace="0">';

	$_MYSTERY['this_select_access'] = 'no';
	$_MYSTERY['this_insert_access'] = 'no';
	$_MYSTERY['this_update_access'] = 'no';
	$_MYSTERY['this_delete_access'] = 'no';
	
	$_MYSTERY['administrator_email'] = 'mystery4@burney.ws';
	
	// setup placeholders for future items
	
	$_MYSTERY['custom_menu_items'] = array();
	$_MYSTERY['table_info'] = array();
	$_MYSTERY['query_list'] = array();

	
	// set some defaults for the action and table if not set
	
	if (@$_REQUEST['table'] == '') { $_REQUEST['table'] = 'none'; }
	if (@$_REQUEST['action'] == '') { $_REQUEST['action'] = 'select_table'; }
	


	/*
	
	// Static Scalar Initialization
	
	$auth_method = '';
	$calculate_page_time = 'yes';
	$cmd = '';
	$error_message = '';
	$error_string = '';
	$ep_command = '';
	$glue = '';
	$header_file = './private/includes/header.php';
	$header_included = 'no';
	$footer_file = './private/includes/footer.php';
	$footer_included = 'no';
	$in_admin_group = 'no';
	$installed = 'yes';	
	$key = '';
	$password_cookie = '';
	$processor = '';
	$ret = 0;
	$show_field = '';
	$start_portals = 'no';
	$this_access_type = '';
	$this_auth = '';
	$this_delete_access = 'no';
	$this_include = '';
	$this_insert_access = 'no';
	$this_password = '';
	$this_select_access = 'no';
	$this_update_access = 'no';
	$this_user_id = '';
	$this_user_valid_ip = '';
	$this_username = 'Not Logged In';
	$this_password = '';
	$this_email = '';
	$username_cookie = '';
	$user_is_logged_in = 'no';
	$virus_feedback = '';
	$virus_scanner_command = '';
	
	// Static Array Initialization
	
	$bf_add_fields = array();
	$binary_fields = array();
	$custom_actions = array();
	$custom_menu_items = array();
	$eds_fields = array();
	$feedback = array();
	$field_display = array();
	$find_array = array();
	$main_menu_items = array();
	$search_fields = array();
	$these_files = array();
	$user_groups = array();
	$db = array();
	
	// Conditional Scalar Initialization
	
	if (!isset($action)) { $action = ''; }
	if (!isset($custom_query_string)) { $custom_query_string = ''; }
	if (!isset($field_default)) { $field_default = ''; }
	if (!isset($label)) { $label = ''; }
	if (!isset($logout)) {$logout = ''; }
	if (!isset($order_by)) { $order_by = ''; }
	if (!isset($page)) { $page = ''; }
	if (!isset($prev_page)) { $prev_page = ''; }
	if (!isset($query_string)) { $query_string = ''; }
	if (!isset($reverse_sort)) { $reverse_sort = ''; }
	if (!isset($rows_per_page)) { $rows_per_page = ''; }
	if (!isset($table)) { $table = ''; }
	if (!isset($type)) { $type = ''; }
	if (!isset($value)) { $value = ''; }
	if (!isset($c)) { $c = ''; }
	if (!isset($confirm_password)) { $confirm_password = ''; }
	if (!isset($email_address)) { $email_address = ''; }
	if (!isset($source)) { $source = ''; }
	
	// Conditional Array Initialization
	
	if (!isset($delete_rows)) { $delete_rows = array(); }
	if (!isset($portal_values)) { $portal_values = array(); }

	*/

}

?>
