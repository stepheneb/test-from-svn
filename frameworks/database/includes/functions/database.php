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

functions/database.php

This is the database function file.  These functions are wrappers
for database calls, such as connecting, disconnecting, performing
queries, etc.

**************************************************************/

function mystery_db_connect($connection = 'dbh', $connection_string = '') {

	// this function connects to the database.  You can provide an alternate
	// connection name to be used as the reference.  'dbh' is reserved for the
	// main mystery application. $connection string should be a standard PEAR::DB
	// connection string: dbtype://username:password@server/database

	global $_MYSTERY;
	
	if ($connection == 'dbh') {
		$connection_string = $_MYSTERY['db_connect_string'];
	}
	
	if (!is_object(@$_MYSTERY[$connection])) {
	
		$options = array(
			'debug' => 2,
			'persistent' => $_MYSTERY['use_persistent_connections'],
			'portability' => DB_PORTABILITY_ALL
		);
	
		$_MYSTERY[$connection] =& DB::connect($connection_string, $options);
	
		if (DB::isError($_MYSTERY[$connection])) {
			trigger_error('Could not connect to database: ' . $_MYSTERY[$connection]->getMessage() . "\n\n" . $_MYSTERY[$connection]->getDebugInfo(), E_USER_ERROR);
		} else {
			// set some additional options for the connection
			$_MYSTERY[$connection]->setFetchMode(DB_FETCHMODE_ASSOC);
		}
	
	}

}

function mystery_db_disconnect($connection = 'dbh') {

	// this rarely used function disconnects from the database.  If you have non-persistent
	// connections, this is done automatically at the end of the script so there is no
	// reason to manually disconnect it.

	global $_MYSTERY;
	
	if (is_resource(@$_MYSTERY[$connection])) {

		$_MYSTERY[$connection] =& DB::disconnect(MYSTERY_DB_CONNECT_STRING);
	
		if (DB::isError($_MYSTERY[$connection])) {
			trigger_error('Could not connect to database: ' . $_MYSTERY[$connection]->getMessage() . "\n\n" . $_MYSTERY[$connection]->getDebugInfo(), E_USER_WARNING);
		}
	
	}

}

function mystery_count_query($query, $params, $connection = 'dbh') {

	// This function performs a SELECT query with parameters and returns the count of the complete result set
	// It is intended to be used to display the total records available for a query that has been limited
	
	global $_MYSTERY;
		
	// Re-form the query to just get a count
	
	$query = preg_replace('~SELECT(.*?)FROM~isu', 'SELECT COUNT(*) FROM', $query);
	
	$result =& $_MYSTERY[$connection]->getOne($query, $params);
	
	if (DB::isError($result)) {
		trigger_error('Problem with COUNT query: ' . $result->getMessage() . "\n\n" . $result->getDebugInfo(), E_USER_WARNING);
		$result = '';
	}

	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	return $result;

}

function mystery_select_query($query, $params, $connection = 'dbh') {

	// This function performs a SELECT query with parameters and returns all results

	global $_MYSTERY;
	
	mystery_db_connect($connection);
		
	$sth = $_MYSTERY[$connection]->prepare($query);
	
	$result =& $_MYSTERY[$connection]->execute($sth, $params);
	
	$result_array = array();
	
	if (DB::isError($result)) {
		trigger_error('Problem with SELECT query: ' . $result->getMessage() . "\n\n" . $result->getDebugInfo(), E_USER_WARNING);
	} else {
		if ($result->numRows() > 0) {
			while ($row =& $result->fetchRow()) {
				$result_array[] = $row;
			}
		}
	}
	
	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	return $result_array;

}

function mystery_limited_query($query, $params, $offset = 0, $count = 100, $connection = 'dbh') {

	// This function performs a SELECT query with parameters and returns $count results starting at offset

	global $_MYSTERY;
	
	mystery_db_connect($connection);
	
	$query = mystery_convert_placeholder_query($query, $params);
	
	if ($offset < 0) { $offset = 0; }
	
	if ($count <= 0 || $count == '') {
		$count = 100;
	}
	
	$result =& $_MYSTERY[$connection]->limitQuery($query, $offset, $count);
	
	$result_array = array();
	
	if (DB::isError($result)) {
		trigger_error('Problem with LIMITED SELECT query: ' . $result->getMessage() . "\n\n" . $result->getDebugInfo(), E_USER_WARNING);
	} else {
		if ($result->numRows() > 0) {
			while ($row =& $result->fetchRow()) {
				$result_array[] = $row;
			}
		}
		$result->free();
	}

	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	return $result_array;

}


function mystery_insert_query($table, $data, $pk, $connection = 'dbh') {

	// This function performs an INSERT query
	// with parameters and returns the sequence ID
	// $data should be an associative array of name/value pairs
	// MySQL hack -- Since PEAR:DB doesn't use the autoincrement
	// field, we'll manually use it
	
	global $_MYSTERY;
	
	mystery_db_connect($connection);
		
	if (!preg_match('~^mysql~', $_MYSTERY[$connection]->phptype)) {
		$id = $_MYSTERY[$connection]->nextId($table);
		$data[$pk] = $id;
	}
	
	$result = $_MYSTERY[$connection]->autoExecute($table, $data, DB_AUTOQUERY_INSERT);
	
	if (DB::isError($result)) {
		trigger_error('Problem with INSERT query: ' . $result->getMessage() . "\n\n" . $result->getDebugInfo(), E_USER_WARNING);
		return 0;
	} else {
		if (!preg_match('~^mysql~', $_MYSTERY[$connection]->phptype)) {
			return $id;
		} else {
			return mysql_insert_id($_MYSTERY[$connection]->connection);
		}
	}

}

function mystery_update_query($table, $data, $pk, $pk_value, $connection = 'dbh') {

	// This function performs an UPDATE query
	// with parameters and returns the number of rows affected
	// $data should be an associative array of name/value pairs
	// $pk and $pK_value determine which rows are updated. 
	//
	// $pk should be an array of key fields, $pk_value should be an array of values for the keys

	global $_MYSTERY;
	
	if (!is_array($pk)) { $pk = array($pk); }
	
	if (!is_array($pk_value)) { $pk_value = array($pk_value); }
	
	mystery_db_connect($connection);
	
	$conditions = array();
	
	for ($i = 0; $i < count($pk); $i++) {
	
		$conditions[] = $pk[$i] . ' = ' . $_MYSTERY[$connection]->quoteSmart($pk_value[$i]);
	
	}
	
	$condition = implode(' AND ', $conditions);
		
	$result = $_MYSTERY[$connection]->autoExecute($table, $data, DB_AUTOQUERY_UPDATE, $condition);
	
	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	if (DB::isError($result)) {
		trigger_error('Problem with UPDATE query: ' . $result->getMessage() . "\n\n" . $result->getDebugInfo(), E_USER_WARNING);
		return 0;
	} else {
		$affected_rows = $_MYSTERY[$connection]->affectedRows();
		if (DB::isError($affected_rows)) {
			trigger_error('Problem getting affected rows: ' . $affected_rows->getMessage() . "\n\n" . $affected_rows->getDebugInfo(), E_USER_WARNING);
			return 0;
		} else {
			// this works around the issue of mysql not updating rows if the info is the same
			if ($affected_rows == 0) { $affected_rows = -1; }
			return $affected_rows;
		}
	}

}


function mystery_replace_query($table, $data, $pk, $pk_value, $connection = 'dbh') {

	// This function first does a search for a record matching the conditions
	// set forth in $pk and $pk_value.  If it finds a record, it does an update using data.
	// If it doesn't, it inserts a new record.
	//
	// $pk should be an array of key fields, $pk_value should be an array of values for the keys

	global $_MYSTERY;
	
	if (!is_array($pk)) { $pk = array($pk); }
	
	if (!is_array($pk_value)) { $pk_value = array($pk_value); }
	
	mystery_db_connect($connection);
	
	$conditions = array();
	
	for ($i = 0; $i < count($pk); $i++) {
	
		$conditions[] = $pk[$i] . ' = ' . $_MYSTERY[$connection]->quoteSmart($pk_value[$i]);
	
	}
	
	$condition = implode(' AND ', $conditions);
	
	$q = 'SELECT * FROM ' . $table . ' WHERE ' . $condition;
	$p = array();
	
	$existing_records = mystery_count_query($q, $p, $connection);
	
	if ($existing_records == 0) {
	
		// record doesn't exist... Do an insert
		
		// add the additional fields to the data array for the pk fields and values
	
		for ($i = 0; $i < count($pk); $i++) {
		
			$data[$pk[$i]] = $pk_value[$i];
		
		}

		$result = $_MYSTERY[$connection]->autoExecute($table, $data, DB_AUTOQUERY_INSERT, $condition);	
	
	} else {
	
		// record exists, do the update instead
	
		$result = $_MYSTERY[$connection]->autoExecute($table, $data, DB_AUTOQUERY_UPDATE, $condition);
	
	}
	
	//mystery_print_r($existing_records, $conditions, $data);
	
	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	if (DB::isError($result)) {
		trigger_error('Problem with REPLACE query: ' . $result->getMessage() . "\n\n" . $result->getDebugInfo(), E_USER_WARNING);
		return 0;
	} else {
		$affected_rows = $_MYSTERY[$connection]->affectedRows();
		if (DB::isError($affected_rows)) {
			trigger_error('Problem getting affected rows: ' . $affected_rows->getMessage() . "\n\n" . $affected_rows->getDebugInfo(), E_USER_WARNING);
			return 0;
		} else {
			// this works around the issue of mysql not updating rows if the info is the same
			if ($affected_rows == 0) { $affected_rows = 1; }
			return $affected_rows;
		}
	}

}



function mystery_delete_query($query, $params, $connection = 'dbh') {

	// This function performs a DELETE query with parameters, similar to
	// the mystery_select_query function.  The only difference is that this
	// function returns the affected rows and not data rows

	global $_MYSTERY;
	
	mystery_db_connect($connection);
		
	$sth = $_MYSTERY[$connection]->prepare($query);
	
	$result =& $_MYSTERY[$connection]->execute($sth, $params);
		
	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	if (DB::isError($result)) {
		trigger_error('Problem with DELETE query: ' . $result->getMessage(), E_USER_WARNING);
		return false;
	} else {
		$affected_rows = $_MYSTERY[$connection]->affectedRows();
		if (DB::isError($affected_rows)) {
			trigger_error('Problem getting affected DELETED rows: ' . $affected_rows->getMessage(), E_USER_WARNING);
			return 0;
		} else {
			return $affected_rows;
		}
	}


}


function mystery_general_query($query, $params, $connection = 'dbh') {

	// This function performs a general query with parameters, similar to
	// the mystery_select_query function.  The only difference is that this
	// function returns the affected rows and not data rows

	global $_MYSTERY;
	
	mystery_db_connect($connection);
		
	$sth = $_MYSTERY[$connection]->prepare($query);
	
	$result =& $_MYSTERY[$connection]->execute($sth, $params);
		
	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	if (DB::isError($result)) {
		trigger_error('Problem with GENERAL query: ' . $result->getMessage(), E_USER_WARNING);
		return false;
	} else {
		$affected_rows = $_MYSTERY[$connection]->affectedRows();
		if (DB::isError($affected_rows)) {
			trigger_error('Problem getting affected GENERAL rows: ' . $affected_rows->getMessage(), E_USER_WARNING);
			return 0;
		} else {
			// this works around the issue of mysql not updating rows if the info is the same
			if ($affected_rows == 0) { $affected_rows = -1; }
			return $affected_rows;
		}
	}


}


function mystery_search_query($table, $data, $pk, $glue = ' AND ', $connection = 'dbh') {

	// This function performs an SELECT query
	// matching any rows that match the $data array, using the
	// $glue to determine whether or not to boolean AND or OR
	// 
	// Need to add the search options here, contains, between, in, etc.
	// 

	global $_MYSTERY;
	
	if (!is_array($pk)) { $pk = array($pk); }
	
	if (!is_array($pk_value)) { $pk_value = array($pk_value); }
	
	mystery_db_connect($connection);
	
	$conditions = array();
	
	for ($i = 0; $i < count($pk); $i++) {
	
		$conditions[] = $pk[$i] . ' = ' . $_MYSTERY[$connection]->quoteSmart($pk_value[$i]);
	
	}
	
	$condition = implode(' AND ', $conditions);
		
	$result = $_MYSTERY[$connection]->autoExecute($table, $data, DB_AUTOQUERY_UPDATE, $condition);
	
	$_MYSTERY['query_list'][] = $_MYSTERY[$connection]->last_query;

	if (DB::isError($result)) {
		trigger_error('Problem with UPDATE query: ' . $result->getMessage() . "\n\n" . $result->getDebugInfo(), E_USER_WARNING);
		return 0;
	} else {
		$affected_rows = $_MYSTERY[$connection]->affectedRows();
		if (DB::isError($affected_rows)) {
			trigger_error('Problem getting affected rows: ' . $affected_rows->getMessage() . "\n\n" . $affected_rows->getDebugInfo(), E_USER_WARNING);
			return 0;
		} else {
			// this works around the issue of mysql not updating rows if the info is the same
			if ($affected_rows == 0) { $affected_rows = 1; }
			return $affected_rows;
		}
	}

}


function mystery_convert_placeholder_query($query, $params, $connection = 'dbh') {

	// This function takes a query with placeholders, i.e., ?, and
	// an array of parameters and makes a standard query from it.
	
	global $_MYSTERY;
	
	$i = 0;
	while (($j = strpos($query,'?')) !== false) {
		$query = substr($query,0,$j) . $_MYSTERY[$connection]->quoteSmart($params[$i]) . substr($query,$j+1);
		$i++;
	}
	
	return $query;	

}

function mystery_convert_csv_to_concat($string, $connection = 'dbh') {

	// This function takes a csv list and converts it to an SQL concatenation	

	if (preg_match('~^mysql~', $_MYSTERY[$connection]->phptype)) {
		return 'CONCAT(' . $string . ')';
	}
	
	$c_op = ' || ';

	if (preg_match('~^mysql~', $_MYSTERY[$connection]->phptype)) {
		$c_op = ' + ';
	}
	
	$concat_parts = mystery_csv_tokenize($string, 0);

	return implode($c_op, $concat_parts);

}

function mystery_csv_tokenize($data, $strip = 1) {

	// this function takes in a CSV data string and returns an array
	
	$output = array();
	$j = 0;
	$temp = '';
	$quote = '';
	$within_field = 0;

	for ($i = 0; $i < strlen($data); $i++) {
	
		if ($quote == '' && preg_match('~["\']~', $data[$i])) { $quote = $data[$i]; }
		
		if ($data[$i] == $quote && $within_field == 0) {

			$within_field = 1;

		} elseif ($data[$i] == $quote && $within_field == 1) {

			if (@$data[($i - 1)] != '\\') { // don't end the field if this is just an escaped quote
				$within_field = 0;
			}

		}
		
		if ($i + 1 == strlen($data)) {
			// we are at the end of the string, so output whatever is in temp
			$temp .= $data[$i];
			if ($strip == 1) {
				$output[] = str_replace('\\' . $quote, $quote, $temp);
			} else {
				$output[] = $temp;
			}
		} elseif ($data[$i] == ',' && $within_field == 0) {
			// we have found a comma not in a quoted string, so we should set the prevous contents as a field
			if ($strip == 1) {
				$output[] = str_replace('\\' . $quote, $quote, $temp);
			} else {
				$output[] = $temp;
			}
			$temp = ''; // re-initialize the temp variable
		} else {
			// no comma yet so just append to the temp variable, stripping if necessary
			if ($data[$i] == $quote && $strip == 1 && @$data[($i - 1)] != '\\') {
				$temp .= '';
			} else {
				$temp .= $data[$i];
			}
		}

	}
	
	return $output;
	
}


?>
