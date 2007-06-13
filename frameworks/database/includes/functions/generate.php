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

functions/generate.php

This is the generate functions file.  Any functions that use
programming to generate something used somewhere are in this file.

**************************************************************/

function mystery_generate_quick_nav() {

	// this function generates the table list for quick navigation
	
	global $_MYSTERY;
	
	$quick_nav = '';
	
	$links = array();
	$labels = array();
	
	$menu_items = mystery_get_main_menu_items();
	
	reset($menu_items);
	
	while (list($key, $value) = each($menu_items['name'])) {
	
		$links[] = $_SERVER['SCRIPT_NAME'] . '?action=' . $menu_items['action'][$key] . '&amp;table=' . $menu_items['table'][$key];
		$labels[] = htmlspecialchars($value);
	
	}
	
	$quick_nav .= '
	<form action="' . $_SERVER['SCRIPT_NAME'] . '?action=redirect" method="post" class="NoPaddingNoMargin">
	<select name="location" onchange="document.location=location.value;" class="QuickNav">
	<option value="">Quick Navigation</option>
	<option value="">-----------------------</option>
	';

	for ($i = 0; $i < count($links); $i++) {
		$quick_nav .= '
		<option value="' . $links[$i] . '">' . $labels[$i] . '</option>';	
	}

	$quick_nav .= '
	</select>
	<noscript>
	<input type="submit" value="Go">
	</noscript>
	</form>
	';
	
	return $quick_nav;

}


function mystery_generate_options_menu() {

	// this function creates the option menu.

	global $_MYSTERY;
	
	$menu_display_global = array();
	$menu_display_local = array();

	$menu_parts_global['action'][] = 'select_table';
	$menu_parts_global['label'][] = 'Main Menu';
	$menu_parts_global['access_key'][] = 'm';
	$menu_parts_global['description'][] = 'Select a new ' . ucwords($_MYSTERY['word_that_means_table']);
	
	$menu_parts_global['action'][] = 'help';
	$menu_parts_global['label'][] = 'Help';
	$menu_parts_global['access_key'][] = 'h';
	$menu_parts_global['description'][] = 'View help for using this system.';
	
	$menu_parts_global['action'][] = 'logout';
	$menu_parts_global['label'][] = 'Logout';
	$menu_parts_global['access_key'][] = 'l';
	$menu_parts_global['description'][] = 'Logout from the system.';
	
	$menu_parts_local['action'][] = 'view_data';
	$menu_parts_local['label'][] = 'Browse';
	$menu_parts_local['access_key'][] = 'b';
	$menu_parts_local['description'][] = 'Browse all records in this ';
	
	$menu_parts_local['action'][] = 'find_data';
	$menu_parts_local['label'][] = 'Search';
	$menu_parts_local['access_key'][] = 's';
	$menu_parts_local['description'][] = 'Perform a search on this ' . ucwords($_MYSTERY['word_that_means_table']);

	$menu_parts_local['action'][] = 'add_data';
	$menu_parts_local['label'][] = 'Add';
	$menu_parts_local['access_key'][] = 'a';
	$menu_parts_local['description'][] = 'Add a new record to this ' . ucwords($_MYSTERY['word_that_means_table']);
	
	reset($_MYSTERY['custom_menu_items']);
	while (list($action,$label) = each($_MYSTERY['custom_menu_items'])) {
		$menu_parts_local['action'][] = $action;
		$menu_parts_local['label'][] = $label;
		$menu_parts_local['access_key'][] = $key;
		$menu_parts_local['description'][] = 'Custom Menu Item: ' . $label;
	}
	
	for ($i = 0; $i < count($menu_parts_global['action']); $i++) {
	
		if (@$_SESSION['is_logged_in'] == 'yes') {
			if ($_REQUEST['action'] == $menu_parts_global['action'][$i]) {
				$menu_display_global[] = '<span class="SelectedOptionsMenuItem">' . $menu_parts_global['label'][$i] . '</span>';
			} else {
				if (
					($menu_parts_global['action'][$i] == 'add_data' && $_MYSTERY['this_insert_access'] != 'yes')
					|| ($menu_parts_global['action'][$i] == 'view_data' && $_MYSTERY['this_select_access'] != 'yes')
					|| ($menu_parts_global['action'][$i] == 'find_data' && $_MYSTERY['this_select_access'] != 'yes')
				) {
					$menu_display_global[] = '<span class="DisabledOptionsMenuItem">' . $menu_parts_global['label'][$i] . '</span>';
				} else {
					$this_link = $_SERVER['SCRIPT_NAME'] . '?action=' . $menu_parts_global['action'][$i] . '&amp;table=' . $_REQUEST['table'];
					$menu_display_global[] = '<a href="' . $this_link . '" title="' . $menu_parts_global['description'][$i] . '" accesskey="' . $menu_parts_global['access_key'][$i] . '" class="OptionsMenuItem">' . $menu_parts_global['label'][$i] . '</a> <a href="' . $this_link . '" title="Open in a New Window">' . $_MYSTERY['new_window_image'] . '</a>';
				}
			}
		} else {
			$menu_display_global[] = '<span class="DisabledOptionsMenuItem">' . $menu_parts_global['label'][$i] . '</span>';
		}
	
	}

	for ($i = 0; $i < count($menu_parts_local['action']); $i++) {
	
		if (@$_SESSION['is_logged_in'] == 'yes') {
			if ($_REQUEST['action'] == $menu_parts_local['action'][$i]) {
				$menu_display_local[] = '<span class="SelectedOptionsMenuItem">' . $menu_parts_local['label'][$i] . '</span>';
			} else {
				if (
					($menu_parts_local['action'][$i] == 'add_data' && $_MYSTERY['this_insert_access'] != 'yes')
					|| ($menu_parts_local['action'][$i] == 'view_data' && $_MYSTERY['this_select_access'] != 'yes')
					|| ($menu_parts_local['action'][$i] == 'find_data' && $_MYSTERY['this_select_access'] != 'yes')
				) {
					$menu_display_local[] = '<span class="DisabledOptionsMenuItem">' . $menu_parts_local['label'][$i] . '</span>';
				} else {
					$menu_display_local[] = '<span class="OptionsMenuItem"><a href="' . $_SERVER['SCRIPT_NAME'] . '?action=' . $menu_parts_local['action'][$i] . '&amp;table=' . $_REQUEST['table'] . '" title="' . $menu_parts_local['description'][$i] . '" accesskey="' . $menu_parts_global['access_key'][$i] . '">' . $menu_parts_local['label'][$i] . '</a></span>';
				}
			}
		} else {
			$menu_display_local[] = '<span class="DisabledOptionsMenuItem">' . $menu_parts_local['label'][$i] . '</span>';
		}
	
	}

	$options_menu = '' . implode(" | \n\t", $menu_display_global) . '<br>' . implode(" | \n\t", $menu_display_local) . '';
	
	return $options_menu;
	
}


function mystery_generate_next_prev_links($offset, $count, $total, $offset_var, $count_var, $base_url, $separator) {

	global $_MYSTERY;
	
	$link_string = '';
	$link_string_parts = array();
	
	if ($total == 0) { return $link_string; }
	
	$query_string_mark = '?';
	
	if (preg_match('~\?~', $base_url)) {
		$query_string_mark = '&amp;';
	}
	
	// Sanity check and setup next/prev variables
	
	if ($offset == '') { $offset = 0; }

	if ($offset < 0) { $offset = 0; }

	if ($count == '' || $count <= 0) { $count = 25; }

	if ($offset > $total) { $offset = $total - $count; }
	
	$prev = $offset - $count;
	$next = $offset + $count;
	
	if ($prev < 0) { $prev = 0; }

	if ($offset > 0) {
	
		// there is an offset, so we can have a previous link
		
		$link_string_parts[] = '<a href="' . $base_url . $query_string_mark . $offset_var . '=0&amp;' . $count_var . '=' . $count . '">First ' . $count . '</a>';
		
		$link_string_parts[] = '<a href="' . $base_url . $query_string_mark . $offset_var . '=' . $prev . '&amp;' . $count_var . '=' . $count . '">Previous ' . $count . '</a>';
		
	
	}
		
	if (($offset + $count) < $total) {
		
		$link_string_parts[] = '<a href="' . $base_url . $query_string_mark . $offset_var . '=' . $next . '&amp;' . $count_var . '=' . $count . '">Next ' . $count . '</a>';

		$link_string_parts[] = '<a href="' . $base_url . $query_string_mark . $offset_var . '=' . ($total - $count) . '&amp;' . $count_var . '=' . $count . '">Last ' . $count . '</a>';
		
	}
	
	$link_string = implode($separator, $link_string_parts);
	
	return $link_string;

}

function mystery_generate_db_value_list() {

	global $_MYSTERY;
	

}

?>
