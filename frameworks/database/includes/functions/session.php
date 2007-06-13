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

functions/session.php

This is the file containing the custom session handler code.
This allows Mystery to bypass the built-in PHP session handling
and to store session data within the database.

**************************************************************/

function mystery_setup_default_session() {

	// this function sets up the default session values
	
	$_SESSION['is_logged_in'] = 'no';
	$_SESSION['is_administrator'] = 'no';
	$_SESSION['user_username'] = 'not_logged_in';
	$_SESSION['user_first_name'] = '';
	$_SESSION['user_last_name'] = 'Not Logged In';
	$_SESSION['user_id'] = '0';
	$_SESSION['mystery_login'] = 'no';

}

function mystery_session_open($save_path, $session_name) {

	global $_MYSTERY;

	mystery_db_connect();
	
	return true;

}

function mystery_session_close() {

	global $_MYSTERY;

	if (is_resource($_MYSTERY['dbh'])) {
		mystery_db_disconnect();
	} else {
		return true;
	}

}

function mystery_session_read($id) {

	// this function gets session data from the database	

	global $_MYSTERY;

	$maxlifetime = ini_get('session.gc_maxlifetime');
	$table = $_MYSTERY['table_prefix'] . 'sessions';
	$cutoff = date('YmdHis', time() - $maxlifetime);

	$query = 'SELECT session_data FROM ' . $table . ' WHERE session_key = ? AND session_timestamp > ?';
	
	$params = array($id, $cutoff);

	// mystery_print_r($query, $params); exit;

	$session_data = mystery_select_query($query, $params);
	
	if (count($session_data) > 0) {
		return $session_data[0]['session_data'];
	} else {
		return '';
	}

}

function mystery_session_write($id, $mystery_session_data) {

	global $_MYSTERY;

	$table = $_MYSTERY['table_prefix'] . 'sessions';
	
	$query = 'DELETE FROM ' . $table . ' WHERE session_key = ?';
	$params = array($id);

	mystery_delete_query($query, $params);
	
	$pk = 'session_id';
	$data['session_key'] = $id;
	$data['session_timestamp'] = date('YmdHis');
	$data['session_data'] = $mystery_session_data;
	
	mystery_insert_query($table, $data, $pk);

	return true;

}

function mystery_session_destroy($id) {

	global $_MYSTERY;
	
	$table = $_MYSTERY['table_prefix'] . 'sessions';
	
	$query = 'DELETE FROM ' . $table . ' WHERE session_key = ?';
	$params = array($id);

	mystery_delete_query($query, $params);

	//trigger_error('session destruction occurred ' . __FILE__ . ':' . __LINE__ . ':(');

	return true;

}

function mystery_session_gc($maxlifetime) {

	// Delete any sessions older than $maxlifetime seconds old

	global $_MYSTERY;

	$table = $_MYSTERY['table_prefix'] . 'sessions';
	
	$cutoff = date('YmdHis', time() - $maxlifetime);
	
	$query = 'DELETE FROM ' . $table . ' WHERE session_timestamp < ?';
	$params = array($cutoff);

	mystery_delete_query($query, $params);

	// trigger_error('gc occurred ' . __FILE__ . ':' . __LINE__ . ':(');

	return true;
	
}

?>
