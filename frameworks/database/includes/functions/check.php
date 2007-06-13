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

functions/check.php

This is the check functions file.  The functions listed in
this file check various conditions, returning booleans.

**************************************************************/


function mystery_check_installation_status() {

	// this function finds out what tables exist
	// for this connection to see if Mystery is installed
	// or if it is just misconfigured. 

	global $_MYSTERY;

	
	
}


/*function mystery_check_user_table_access() {

	// this function checks to see if a user has access to a table and returns the table information
	
	global $_MYSTERY;

// Query to see if this user has access to this table.

if ($_SESSION['is_administrator'] == 'yes') {
	$query = 'SELECT * FROM ' . $_MYSTERY['table_prefix'] . 'tables WHERE table_id = ?';
	$params = array($table_id);
} else {
	$query = 'SELECT * FROM ' . $_MYSTERY['table_prefix'] . 'groups_tables AS gtt LEFT JOIN ' . $_MYSTERY['table_prefix'] . 'tables AS tt ON gtt.table_id=tt.table_id WHERE gtt.table_id = ? AND group_id IN ("' . implode('","', $_SESSION['user_groups']) . '") ORDER BY access_type DESC';
	$params = array($table_id);
}*/


?>
