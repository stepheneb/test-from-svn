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

functions/get.php

This is the get functions file.  Any functions that talk to data
sources and fetch items into lists, etc for later use are in this
file.

**************************************************************/

function mystery_get_main_menu_items() {

	// This function gets all of the tables and custom main menu items and places them in an array
	
	global $_MYSTERY;
	
	// Initialize an array to store the tables we find.  
	// Make it static so if called twice on the page (select_table and quick nav), it won't be generated twice.

	static $menu_items = array();
	
	if (count($menu_items) > 0) { return $menu_items; }
	
	// Add the Update Personal Info item as the first item if logged in via Mystery

	if ($_SESSION['mystery_login'] == 'yes') {
		$menu_items['name'][] = '   Update Personal Information - (Password, etc.)';
		$menu_items['action'][] = 'user_info';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'Update your personal and login info, change your password, etc.';
	}
	
	// Find the custom menu items (if any)

	if ($_SESSION['is_administrator'] == 'yes') {
		$query = 'SELECT menu_item_name,menu_item_action FROM ' . $_MYSTERY['table_prefix'] . 'custom_main_menu_items WHERE menu_item_status="Active"';
		$params = array();
	} else {
		$query = 'SELECT menu_item_name,menu_item_action FROM ' . $_MYSTERY['table_prefix'] . 'custom_main_menu_items WHERE menu_item_status="Active" AND menu_item_group_restriction IN ("0", "' . implode('","', $_SESSION['user_groups']) . '")';
		$params = array();
	}
	
	$results = mystery_select_query($query, $params);
		
	for ($i = 0; $i < count($results); $i++) {
	
		// add items to table list	
		$menu_items['name'][] = $results[$i]['menu_item_name'];
		$menu_items['action'][] = $results[$i]['menu_item_action'];
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = $results[$i]['menu_item_name'];
		
	}
	
	// Connect to the groups_tables table to find out what tables the
	// user has access to.  Also add the administrator options.
	
	if ($_SESSION['is_administrator'] == 'yes') {
	
		$menu_items['name'][] = ' Admin - Reports: Recent Logins';
		$menu_items['action'][] = 'user_recent';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'See which users (top 50) have logged on recently';
	
		$menu_items['name'][] = ' Admin - Reports: Never Logged In';
		$menu_items['action'][] = 'user_never';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'See which users have never logged on.';
	
		$menu_items['name'][] = ' Admin - Reports: Failed Logins';
		$menu_items['action'][] = 'user_failed';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'See the most recent failed login attempts.';
	
		$menu_items['name'][] = ' Admin - Reports: Security Logs';
		$menu_items['action'][] = 'security_log';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'Bresult[$i]se the security log';
	
		$menu_items['name'][] = ' Admin - Reports: Error Logs';
		$menu_items['action'][] = 'error_log';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'Bresult[$i]se the error log';
	
		$menu_items['name'][] = ' Admin - Setup: Additional Installations';
		$menu_items['action'][] = 'install';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'Install demonstration tables/users and/or sample applications';
	
		$menu_items['name'][] = ' Admin - Docs: System Documentation';
		$menu_items['action'][] = 'documentation';
		$menu_items['table'][] = 'none';
		$menu_items['description'][] = 'Read over the system documentation, written for system administrators';
	
		$query = 'SELECT table_id,table_display_name,table_display_comment,table_default_action FROM ' . $_MYSTERY['table_prefix'] . 'tables';
		$params = array();

	} else {
	
		$query = 'SELECT DISTINCT gtt.table_id as table_id,table_display_name,table_display_comment,table_default_action FROM ' . $_MYSTERY['table_prefix'] . 'groups_tables AS gtt LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS tt ON gtt.table_id=tt.table_id WHERE group_id IN ("' . implode('","', $_SESSION['user_groups']) . '")';
		$params = array();
	
	}

	$results = mystery_select_query($query, $params);
	
	if (count($results) > 0) {
	
		// user has access to these tables
		
		for ($i = 0; $i < count($results); $i++) {
		
			$menu_items['name'][] = $results[$i]['table_display_name'];
			$menu_items['action'][] = $results[$i]['table_default_action'];
			$menu_items['table'][] = $results[$i]['table_id'];
			$menu_items['description'][] = $results[$i]['table_display_comment'];
		
		}
		
	}
	
	asort($menu_items['name']);
	
	return $menu_items;

}


function mystery_get_table_configuration($table_id) {

	// this function gets the configuration information for a particular
	// table and places it in the $_MYSTERY['table_info'] array.  If the
	// info already exists, the function just returns.
	
	global $_MYSTERY;
	
	// put in an array like this $tables[$table_id]['key'] = $value;
	
	if (isset($_MYSTERY['table_info'][$table_id])) { return; }

	// Query to see if this user has access to this table.
	
	if ($_SESSION['is_administrator'] == 'yes') {
		$query = 'SELECT * FROM ' . $_MYSTERY['table_prefix'] . 'tables WHERE table_id = ?';
		$params = array($table_id);
	} else {
		$query = 'SELECT * FROM ' . $_MYSTERY['table_prefix'] . 'groups_tables AS gtt LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS tt ON gtt.table_id=tt.table_id WHERE gtt.table_id = ? AND group_id IN ("' . implode('","', $_SESSION['user_groups']) . '") ORDER BY access_type DESC';
		$params = array($table_id);
	}
	
	$table_info = mystery_select_query($query, $params);

	if (count($table_info) == 0) {
	
		// user has selected a table that he doesn't have access to.  Bad user...
		mystery_log_violation('Purple', 'User entered a table_id they did not have access to');
	
	}
	
	// We only get the first row.  If a user is in more than one group that has access to
	// this table, results will be unpredictable.  The results are sorted by type, so if a
	// user has table access in one of the groups, it should show up above the row level access.
	
	$_MYSTERY['table_info'][$table_id]['database'] = $table_info[0]['table_database'];
	$_MYSTERY['table_info'][$table_id]['real_name'] = $table_info[0]['table_real_name'];
	$_MYSTERY['table_info'][$table_id]['display_name'] = $table_info[0]['table_display_name'];
	$_MYSTERY['table_info'][$table_id]['display_comment'] = $table_info[0]['table_display_comment'];
	$_MYSTERY['table_info'][$table_id]['display_data_word'] = $table_info[0]['table_display_data_word'];
	$_MYSTERY['table_info'][$table_id]['display_field_type'] = $table_info[0]['table_display_field_type'];
	$_MYSTERY['table_info'][$table_id]['display_functions'] = $table_info[0]['table_display_functions'];
	$_MYSTERY['table_info'][$table_id]['default_action'] = $table_info[0]['table_default_action'];
	$_MYSTERY['table_info'][$table_id]['default_query'] = $table_info[0]['table_default_query'];
	$_MYSTERY['table_info'][$table_id]['default_order_field'] = $table_info[0]['table_default_order_field'];
	$_MYSTERY['table_info'][$table_id]['default_reverse_sort'] = $table_info[0]['table_default_reverse_sort'];
	$_MYSTERY['table_info'][$table_id]['default_display'] = $table_info[0]['table_default_display'];
	$_MYSTERY['table_info'][$table_id]['default_display_fields'] = $table_info[0]['table_default_display_fields'];
	$_MYSTERY['table_info'][$table_id]['default_display_rows'] = $table_info[0]['table_default_display_rows'];
	$_MYSTERY['table_info'][$table_id]['default_display_width'] = $table_info[0]['table_default_display_width'];
	$_MYSTERY['table_info'][$table_id]['primary_key'] = $table_info[0]['table_primary_key'];
	$_MYSTERY['table_info'][$table_id]['owner_key'] = $table_info[0]['table_owner_key'];
	$_MYSTERY['table_info'][$table_id]['owner_type'] = $table_info[0]['table_owner_type'];
	$_MYSTERY['table_info'][$table_id]['is_many_to_many'] = $table_info[0]['table_is_many_to_many'];

	if ($_SESSION['is_administrator'] == 'yes') {
	
		// allow administrator all access
	
		$_MYSTERY['table_info'][$table_id]['access_type'] = 'table';
		$_MYSTERY['table_info'][$table_id]['select_access'] = 'yes';
		$_MYSTERY['table_info'][$table_id]['insert_access'] = 'yes';
		$_MYSTERY['table_info'][$table_id]['update_access'] = 'yes';
		$_MYSTERY['table_info'][$table_id]['delete_access'] = 'yes';
		$_MYSTERY['table_info'][$table_id]['effective_group_id'] = '1';
	
	} else {
	
		// set access depending on the user's group's permissions
	
		$_MYSTERY['table_info'][$table_id]['access_type'] = $table_info[0]['access_type'];
		$_MYSTERY['table_info'][$table_id]['select_access'] = $table_info[0]['select_access'];
		$_MYSTERY['table_info'][$table_id]['insert_access'] = $table_info[0]['insert_access'];
		$_MYSTERY['table_info'][$table_id]['update_access'] = $table_info[0]['update_access'];
		$_MYSTERY['table_info'][$table_id]['delete_access'] = $table_info[0]['delete_access'];
		$_MYSTERY['table_info'][$table_id]['effective_group_id'] = $table_info[0]['group_id'];
	
	}
	
	if (
		$_MYSTERY['table_info'][$table_id]['select_access'] != 'yes' &&
		$_MYSTERY['table_info'][$table_id]['insert_access'] != 'yes' &&
		$_MYSTERY['table_info'][$table_id]['update_access'] != 'yes' &&
		$_MYSTERY['table_info'][$table_id]['delete_access'] != 'yes'
	) {
	
		mystery_display_user_error('Cannot access ' . $_MYSTERY['word_that_means_table']);
		echo '
		<p>The groups that you are a member of do not have any access to 
		the ' . $_MYSTERY['word_that_means_table'] . ': 
		' . $_MYSTERY['table_info'][$table_id]['display_name'] . '</p>
		';
		mystery_display_admin_contact_info();
		mystery_footer();
	
	}

	// Get all of the related items for this table
	
	mystery_get_table_owners_list($table_id);

	mystery_get_table_custom_menu_items($table_id);

	mystery_get_table_custom_actions($table_id);

	mystery_get_table_foreign_keys($table_id);

	mystery_get_table_hidden_fields($table_id);

	mystery_get_table_view_only_fields($table_id);

	mystery_get_table_binary_fields($table_id);

	mystery_get_table_custom_triggers($table_id);
	
	mystery_get_table_related_tables($table_id);
	
	mystery_get_table_portal_relation_1($table_id);

	mystery_get_table_portal_relation_2($table_id);

}

function mystery_get_table_portal_relation_1($table_id) {

	// Check to see if another table should be displayed as a portal on the add/edit page
	// This portal type is many to many giving a list of checkboxes on the page (?)

	global $_MYSTERY;

	$pt_user_check = 'gtt.group_id IN ("' . implode('","', $_SESSION['user_groups']) . '") AND ';
	
	if ($_SESSION['is_administrator'] == 'yes') { $pt_user_check = ''; }
	
	$pt_query = 'SELECT DISTINCT sfk1.local_table_field AS f1, sfk2.local_table_field AS f2,st1.table_real_name AS ft, st1.table_id AS fti, st1.table_display_name AS ftd, sfk2.foreign_table_label_field AS xf, sfk2.foreign_table_value_field AS xv, st2.table_real_name AS xt, st2.table_display_name AS xtd, st2.table_id AS xti FROM ' . $_MYSTERY['table_prefix'] . 'foreign_keys AS sfk1 LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS st1 ON sfk1.local_table_id=st1.table_id LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'foreign_keys AS sfk2 ON sfk1.local_table_id=sfk2.local_table_id LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS st2 ON sfk2.foreign_table_id=st2.table_id LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'groups_tables AS gtt ON gtt.table_id=st1.table_id WHERE ' . $pt_user_check. 'sfk1.foreign_table_id = ? AND st1.table_is_many_to_many="yes" AND st2.table_display_in_portal="yes" AND sfk2.foreign_table_id <> ?';
	$params = array($table_id, $table_id);
	
	$pt_result = mystery_select_query($pt_query, $params);

	$_MYSTERY['table_info'][$table_id]['portals'] = array();

	for ($i = 0; $i < count($pt_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['portals']['f1'][] = $pt_result[$i]['f1'];
		$_MYSTERY['table_info'][$table_id]['portals']['f2'][] = $pt_result[$i]['f2'];
		$_MYSTERY['table_info'][$table_id]['portals']['ft'][] = $pt_result[$i]['ft'];
		$_MYSTERY['table_info'][$table_id]['portals']['ftd'][] = $pt_result[$i]['ftd'];
		$_MYSTERY['table_info'][$table_id]['portals']['fti'][] = $pt_result[$i]['fti'];
		$_MYSTERY['table_info'][$table_id]['portals']['xf'][] = $pt_result[$i]['xf'];
		$_MYSTERY['table_info'][$table_id]['portals']['xv'][] = $pt_result[$i]['xv'];
		$_MYSTERY['table_info'][$table_id]['portals']['xt'][] = $pt_result[$i]['xt'];
		$_MYSTERY['table_info'][$table_id]['portals']['xtd'][] = $pt_result[$i]['xtd'];
	}

}

function mystery_get_table_portal_relation_2($table_id) {

	// Check to see if another table should be displayed as a portal on the add/edit page
	// This portal type is many to many giving a list of other tables on the page (?)
	
	global $_MYSTERY;

	$pt2_user_check = ' AND gtt.group_id IN ("' . implode('","', $_SESSION['user_groups']) . '") AND select_access="yes" ';

	if ($_SESSION['is_administrator'] == 'yes') { $pt2_user_check = ''; }
	
	$pt_query2 = 'SELECT DISTINCT local_table_id,local_table_field,table_real_name,table_display_name,table_default_query,table_default_order_field,table_default_reverse_sort,table_primary_key,table_default_display_fields,table_owner_key,table_owner_type,access_type,select_access,insert_access,update_access,delete_access FROM ' . $_MYSTERY['table_prefix'] . 'foreign_keys AS fkt LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'groups_tables AS gtt ON fkt.local_table_id=gtt.table_id LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS tt ON fkt.local_table_id=tt.table_id WHERE foreign_table_id = ? AND table_is_many_to_many="no" AND table_display_in_portal="yes" ' . $pt2_user_check . 'ORDER BY access_type DESC';
	$params = array($table_id);
	
	$pt_result2 = mystery_select_query($pt_query2, $params);

	$_MYSTERY['table_info'][$table_id]['portals2'] = array();
	$_MYSTERY['table_info'][$table_id]['portals2']['table_name'] = array();

	for ($i = 0; $i < count($pt_result2); $i++) {
	
		if (!in_array($pt_result2[$i]['table_real_name'], $portals2['table_name'])) {
			$_MYSTERY['table_info'][$table_id]['portals2']['table_name'][] = $pt_result2[$i]['table_real_name'];
			$_MYSTERY['table_info'][$table_id]['portals2']['table_display_name'][] = $pt_result2[$i]['table_display_name'];
			$_MYSTERY['table_info'][$table_id]['portals2']['table_id'][] = $pt_result2[$i]['local_table_id'];
			$_MYSTERY['table_info'][$table_id]['portals2']['table_field'][] = $pt_result2[$i]['local_table_field'];
			$_MYSTERY['table_info'][$table_id]['portals2']['table_pk'][] = $pt_result2[$i]['table_primary_key'];
			$_MYSTERY['table_info'][$table_id]['portals2']['display_fields'][] = $pt_result2[$i]['table_default_display_fields'];
			$_MYSTERY['table_info'][$table_id]['portals2']['order_field'][] = $pt_result2[$i]['table_default_order_field'];
			$_MYSTERY['table_info'][$table_id]['portals2']['order_reverse'][] = $pt_result2[$i]['table_default_reverse_sort'];
			$_MYSTERY['table_info'][$table_id]['portals2']['insert_access'][] = $pt_result2[$i]['insert_access'];
			$_MYSTERY['table_info'][$table_id]['portals2']['update_access'][] = $pt_result2[$i]['update_access'];
			$_MYSTERY['table_info'][$table_id]['portals2']['delete_access'][] = $pt_result2[$i]['delete_access'];
			$_MYSTERY['table_info'][$table_id]['portals2']['access_type'][] = $pt_result2[$i]['access_type'];
			$_MYSTERY['table_info'][$table_id]['portals2']['owner_key'][] = $pt_result2[$i]['table_owner_key'];
			$_MYSTERY['table_info'][$table_id]['portals2']['owner_type'][] = $pt_result2[$i]['table_owner_type'];
		}
	
	}

}

function mystery_get_table_owners_list($table_id) {

	// This function sets up the owners list if table has a owner_key
	
	global $_MYSTERY;
	
	if ($_MYSTERY['table_info'][$table_id]['owner_key'] != '') {
	
		if ($_MYSTERY['table_info'][$table_id]['owner_type'] == 'user') {
	
			$_MYSTERY['table_info'][$table_id]['owner_id'] = $_SESSION['user_id'];
			$owners_query = 'SELECT mu.user_username AS owner_name,mu.user_id AS owner_id FROM ' . $_MYSTERY['table_prefix'] . 'groups_tables AS mgt LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'users_groups AS mug ON mug.group_id=mgt.group_id LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'users AS mu ON mug.user_id=mu.user_id WHERE mgt.table_id = ? ORDER BY owner_name';
			$owners_params = array($table_id);
	
		}
	
		if ($_MYSTERY['table_info'][$table_id]['owner_type'] == 'group') {
	
			$_MYSTERY['table_info'][$table_id]['owner_id'] = $_MYSTERY['table_info'][$table_id]['effective_group_id'];
			$owners_query = 'SELECT mg.group_id AS owner_id, mg.group_name AS owner_name FROM ' . $_MYSTERY['table_prefix'] . 'groups_tables AS mgt LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'groups AS mg ON mgt.group_id=mg.group_id WHERE table_id = ? ORDER BY owner_name';
			$owners_params = array($table_id);
	
		}
	
		$owners_result = mystery_select_query($owners_query, $owners_params);
	
		$_MYSTERY['table_info'][$table_id]['owners_list'] = array();
	
		for ($i = 0; $i < count($owners_result); $i++) {
			$_MYSTERY['table_info'][$table_id]['owners_list'][$owners_result[$i]['owner_id']] = $owners_result[$i]['owner_name'];
		}
	
	}
	
}


function mystery_get_table_custom_menu_items($table_id) {
	
	// Get the custom menu items for this table
	
	global $_MYSTERY;
	
	if ($_SESSION['is_administrator'] == 'yes') {
		$cm_query = 'SELECT menu_label,menu_file_name FROM ' . $_MYSTERY['table_prefix'] . 'custom_menus AS tcm LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'table_custom_menus AS cm ON tcm.menu_id=cm.menu_id WHERE table_id = ?';
		$params = array($table_id);
	} else {
		$cm_query = 'SELECT menu_label,menu_file_name FROM ' . $_MYSTERY['table_prefix'] . 'custom_menus AS tcm LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'table_custom_menus AS cm ON tcm.menu_id=cm.menu_id WHERE table_id = ? AND cm_group_restriction IN ("0", "' . implode(',', $_SESSION['user_groups']) . '")';
		$params = array($table_id);
	}
	
	$cm_result = mystery_select_query($cm_query, $params);

	$_MYSTERY['table_info'][$table_id]['custom_menu_items'] = array();

	for ($i = 0; $i < count($cm_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['custom_menu_items'][$cm_results[$i]['menu_label']] = $cm_results[$i]['menu_file_name']; 
	}

}

function mystery_get_table_custom_actions($table_id) {

	// Get the actions for this table
	
	global $_MYSTERY;
	
	if ($_SESSION['is_administrator'] == 'yes') {
		$ca_query = 'SELECT action_label,action_file_name FROM ' . $_MYSTERY['table_prefix'] . 'custom_actions AS tca LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'table_custom_actions AS ca ON tca.action_id=ca.action_id WHERE table_id = ?';
		$params = array($table_id);
	} else {
		$ca_query = 'SELECT action_label,action_file_name FROM ' . $_MYSTERY['table_prefix'] . 'custom_actions AS tca LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'table_custom_actions AS ca ON tca.action_id=ca.action_id WHERE table_id = ? AND ca_group_restriction IN ("0", "' . implode(',', $_SESSION['user_groups']) . '")';
		$params = array($table_id);
	}
	
	$ca_result = mystery_select_query($ca_query, $params);

	$_MYSTERY['table_info'][$table_id]['custom_actions'] = array();

	for ($i = 0; $i < count($ca_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['custom_actions'][$ca_result[$i]['action_label']] = $ca_result[$i]['action_file_name']; 
	}

}


function mystery_get_table_foreign_keys($table_id) {
	
	// Get the foreign key info for this table
	
	global $_MYSTERY;
	
	$fk_query = 'SELECT table_real_name AS foreign_table_real_name, table_owner_key AS foreign_table_owner_key, table_owner_type AS foreign_table_owner_type, local_table_field,foreign_table_label_field,foreign_table_value_field,list_display_type,foreign_table_id FROM ' . $_MYSTERY['table_prefix'] . 'foreign_keys AS fk LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS tbl ON fk.foreign_table_id=tbl.table_id WHERE fk.local_table_id = ?';
	$params = array($table_id);

	$fk_result = mystery_select_query($fk_query, $params);

	$_MYSTERY['table_info'][$table_id]['foreign_keys'] = array();

	for ($i = 0; $i < count($fk_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['foreign_keys'][$fk_result[$i]['local_table_field']]['table'] = $fk_result[$i]['foreign_table_real_name']; 
		$_MYSTERY['table_info'][$table_id]['foreign_keys'][$fk_result[$i]['local_table_field']]['owner_key'] = $fk_result[$i]['foreign_table_owner_key']; 
		$_MYSTERY['table_info'][$table_id]['foreign_keys'][$fk_result[$i]['local_table_field']]['owner_type'] = $fk_result[$i]['foreign_table_owner_type']; 
		$_MYSTERY['table_info'][$table_id]['foreign_keys'][$fk_result[$i]['local_table_field']]['label'] = $fk_result[$i]['foreign_table_label_field']; 
		$_MYSTERY['table_info'][$table_id]['foreign_keys'][$fk_result[$i]['local_table_field']]['value'] = $fk_result[$i]['foreign_table_value_field']; 
		$_MYSTERY['table_info'][$table_id]['foreign_keys'][$fk_result[$i]['local_table_field']]['type'] = $fk_result[$i]['list_display_type'];
		$_MYSTERY['table_info'][$table_id]['foreign_keys'][$fk_result[$i]['local_table_field']]['id'] = $fk_result[$i]['foreign_table_id'];
	}
	
}

function mystery_get_table_hidden_fields($table_id) {

	// Get the hidden fields for this table
	
	global $_MYSTERY;
	
	$hf_query = 'SELECT field_name,group_id FROM ' . $_MYSTERY['table_prefix'] . 'group_hidden_fields WHERE table_id = ?';
	$params = array($table_id);

	$hf_result = mystery_select_query($hf_query, $params);

	$_MYSTERY['table_info'][$table_id]['group_hidden_fields'] = array();

	for ($i = 0; $i < count($hf_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['group_hidden_fields'][$hf_result[$i]['group_id']][$hf_results[$i]['field_name']] = 'yes'; 
	}
	
	$hf_query = 'SELECT field_name,user_id FROM ' . $_MYSTERY['table_prefix'] . 'user_hidden_fields WHERE table_id = ?';
	$params = array($table_id);

	$hf_result = mystery_select_query($hf_query, $params);

	$_MYSTERY['table_info'][$table_id]['user_hidden_fields'] = array();

	for ($i = 0; $i < count($hf_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['user_hidden_fields'][$hf_result[$i]['user_id']][$hf_results[$i]['field_name']] = 'yes'; 
	}

}


function mystery_get_table_view_only_fields($table_id) {

	// Get the view only fields for this table
	
	global $_MYSTERY;
	
	$vo_query = 'SELECT field_name,group_id FROM ' . $_MYSTERY['table_prefix'] . 'group_view_only_fields WHERE table_id = ?';
	$params = array($table_id);
	
	$vo_result = mystery_select_query($vo_query, $params);
	
	$_MYSTERY['table_info'][$table_id]['group_view_only_fields'] = array();
	
	for ($i = 0; $i < count($vo_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['group_view_only_fields'][$vo_result[$i]['group_id']][$vo_result[$i]['field_name']] = 'yes'; 
	}
	
	$vo_query = 'SELECT field_name,user_id FROM ' . $_MYSTERY['table_prefix'] . 'user_view_only_fields WHERE table_id = ?';
	$params = array($table_id);

	$vo_result = mystery_select_query($vo_query, $params);
	
	$_MYSTERY['table_info'][$table_id]['user_view_only_fields'] = array();

	for ($i = 0; $i < count($vo_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['user_view_only_fields'][$vo_result[$i]['user_id']][$vo_result[$i]['field_name']] = 'yes'; 
	}

}

function mystery_get_table_binary_fields($table_id) {
	
	// Get the binary file fields for this table
	
	global $_MYSTERY;
	
	$bf_query = 'SELECT filename_field_name,size_field_name,type_field_name,height_field_name,width_field_name,path_to_file,max_size_of_file,rename_file,overwrite_file,external_processor FROM ' . $_MYSTERY['table_prefix'] . 'binary_fields WHERE table_id = ?';
	$params = array($table_id);

	$bf_result = mystery_select_query($bf_query, $params);

	$_MYSTERY['table_info'][$table_id]['binary_fields'] = array();

	for ($i = 0; $i < count($bf_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['destination'] = $bf_result[$i]['path_to_file']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['rename'] = $bf_result[$i]['rename_file']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['overwrite'] = $bf_result[$i]['overwrite_file']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['maxsize'] = $bf_result[$i]['max_size_of_file']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['mime_field'] = $bf_result[$i]['type_field_name']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['size_field'] = $bf_result[$i]['size_field_name']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['height_field'] = $bf_result[$i]['height_field_name']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['width_field'] = $bf_result[$i]['width_field_name']; 
		$_MYSTERY['table_info'][$table_id]['binary_fields'][$bf_result[$i]['filename_field_name']]['processor'] = $bf_result[$i]['external_processor']; 
	}

}

function mystery_get_table_custom_triggers($table_id) {

	// Get the custom triggers for this table
	
	global $_MYSTERY;

	$ct_query = 'SELECT trigger_file_name,trigger_when,trigger_condition FROM ' . $_MYSTERY['table_prefix'] . 'table_custom_triggers AS tct LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'custom_triggers AS ct ON tct.trigger_id=ct.trigger_id WHERE table_id = ?';
	$params = array($table_id);

	$ct_result = mystery_select_query($ct_query, $params);

	$_MYSTERY['table_info'][$table_id]['before_triggers'] = array();
	$_MYSTERY['table_info'][$table_id]['after_triggers'] = array();

	for ($i = 0; $i < count($ct_result); $i++) {

		if ($ct_result[$i]['trigger_when'] == 'before') {
			$_MYSTERY['table_info'][$table_id]['before_triggers'][$ct_result[$i]['trigger_condition']] = $ct_result[$i]['trigger_file_name'];
		} else {
			$_MYSTERY['table_info'][$table_id]['after_triggers'][$ct_result[$i]['trigger_condition']] = $ct_result[$i]['trigger_file_name'];		
		}

	}

}


function mystery_get_table_related_tables($table_id) {

	// Check for any related fields/tables to this table
	// This is used to preserve referential data integrity.
	
	global $_MYSTERY;

	$rd_query = 'SELECT foreign_table_value_field AS field, foreign_table_label_field AS field_display, local_table_field AS related_field, table_real_name AS related_table, table_id AS related_table_id, table_display_name AS related_table_display_name,table_primary_key AS related_table_pk FROM ' . $_MYSTERY['table_prefix'] . 'foreign_keys AS mfk LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS mt ON mfk.local_table_id=mt.table_id WHERE foreign_table_id = ?';
	$params = array($table_id);
	
	$rd_result = mystery_select_query($rd_query, $params);

	$_MYSTERY['table_info'][$table_id]['relations'] = array();

	for ($i = 0; $i < count($rd_result); $i++) {
		$_MYSTERY['table_info'][$table_id]['relations']['field'][] = $rd_result[$i]['field'];
		$_MYSTERY['table_info'][$table_id]['relations']['field_display'][] = $rd_result[$i]['field_display'];
		$_MYSTERY['table_info'][$table_id]['relations']['related_field'][] = $rd_result[$i]['related_field'];
		$_MYSTERY['table_info'][$table_id]['relations']['related_table'][] = $rd_result[$i]['related_table'];
		$_MYSTERY['table_info'][$table_id]['relations']['related_table_id'][] = $rd_result[$i]['related_table_id'];
		$_MYSTERY['table_info'][$table_id]['relations']['related_table_display_name'][] = $rd_result[$i]['related_table_display_name'];
		$_MYSTERY['table_info'][$table_id]['relations']['related_table_pk'][] = $rd_result[$i]['related_table_pk'];
	}

}


function mystery_get_content_type($file) {

	if (function_exists('mime_content_type')) {
	
		return mime_content_type($file);
		
	} else {

		return exec(trim('file -bi \'' . escapeshellarg($file) . '\''));

	}
	
	// could also add something here to use the imagesize/exif functions if all else fails

}

?>
