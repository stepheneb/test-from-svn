<?php

/**********************

The $_PORTAL array is
typically like this:

$_PORTAL['section']
$_PORTAL['activity']
$_PORTAL['action']
$_PORTAL['params']

**********************/

$_PORTAL = array();

$_PORTAL = portal_parse_uri(@$_SERVER['REQUEST_URI']);

$_PORTAL['errors'] = array();

// Get a database connection to the servers used in the application

mystery_db_connect('portal_dbh', $portal_config['portal_database_connection']);
mystery_db_connect('sunflower_dbh', $portal_config['sunflower_database_connection']);
mystery_db_connect('rails_dbh', $portal_config['rails_database_connection']);

// Setup custom authentication using the Mystery auth framework.

$_MYSTERY['external_auth_functions'][] = 'portal_auth';

// Setup configuration values for this session

$_PORTAL['project'] = portal_get_project_key();
$_PORTAL['project_info'] = portal_get_project_info_by_key($_PORTAL['project']);

portal_convert_project_settings_to_local($_PORTAL['project']);

// Revert the project key to get activities working correctly if this is a special -dev project

$_PORTAL['project'] = str_replace('-dev', '', $_PORTAL['project']);

// setup any contstants we want to use

$portal_image_types = array(
	1 => 'GIF',
	2 => 'JPG',
	3 => 'PNG',
	4 => 'SWF',
	5 => 'PSD',
	6 => 'BMP',
	7 => 'TIFF(intel byte order)',
	8 => 'TIFF(motorola byte order)',
	9 => 'JPC',
	10 => 'JP2',
	11 => 'JPX',
	12 => 'JB2',
	13 => 'SWC',
	14 => 'IFF',
	15 => 'WBMP',
	16 => 'XBM'
);


//mystery_db_connect('lhh_dbh', $lhh_config['lhh_database_connection']);

// Setup the LHH array

//$_PORTAL['school_info'] = portal_get_school_info('');


$portal_required = '<span class="required">*</span>';

$portal_required_legend = $portal_required . ' = Required Field';

// function definitions follow

function portal_auth($username, $password) {

	// This function performs standard Mystery authentication
	// It returns an associative array:
	//
	// $user_info['user_username']
	// $user_info['user_first_name']
	// $user_info['user_last_name']
	// $user_info['user_email']
	//
	// Why the username?  Because the user may enter their email address instead.

	global $_MYSTERY, $_PORTAL;

	$user_info = array();

	/* START: Custom Authentication Function */
	
	// what we need to do is check this member in the portal db then in the sunflower db.
	// If the user is in sunlfower and not portal, we create their portal account. 
	// we will also do the diy registration if not yet done here

	$username = strtolower($username);
	$password = strtolower($password);
	
	$password_ue = $password;

	// if the password is 32 characters, it is probably an MD5 so don't hash it
	
	if (strlen($password) != 32) { $password = md5($password); }

	$query = 'SELECT pm.*,ps.school_district AS member_district FROM portal_members AS pm LEFT JOIN portal_schools AS ps ON pm.member_school=ps.school_id WHERE (member_email = ? OR member_username = ?) AND member_password = ?';
	$params = array($username, $username, $password);

	$results = mystery_select_query($query, $params, 'portal_dbh');

	if (count($results) > 0) {

		// setup general user information for Mystery

		$user_info['user_username'] = $results[0]['member_username'];
		$user_info['user_first_name'] = $results[0]['member_first_name'];
		$user_info['user_last_name'] = $results[0]['member_last_name'];
		$user_info['user_email'] = $results[0]['member_email'];

		// setup particular portal session information
		
		$_SESSION['portal']['member_id'] = $results[0]['member_id'];
		$_SESSION['portal']['member_school'] = $results[0]['member_school'];
		$_SESSION['portal']['member_district'] = $results[0]['member_district'];
		$_SESSION['portal']['member_source'] = $results[0]['member_source'];
		$_SESSION['portal']['member_type'] = $results[0]['member_type'];
		$_SESSION['portal']['member_interface'] = $results[0]['member_interface'];
		$_SESSION['portal']['diy_member_id'] = $results[0]['diy_member_id'];
		$_SESSION['portal']['sds_member_id'] = $results[0]['sds_member_id'];
		$_SESSION['portal']['member_username'] = $username;
		$_SESSION['portal']['member_password_ue'] = $password_ue;
		$_SESSION['portal']['taking_course'] = @$results[0]['taking_course'];

		// log this page view

		portal_log_member_access(@$_SESSION['portal']['member_id'], @$_SESSION['portal']['school_id'], 'signin');
		
		session_regenerate_id();

	} else {
	
		sleep(2);
	
	}

	/* END: Custom Authentication Function */

	return $user_info;

}

function portal_get_project_key() {

	$key = portal_get_host_prefix();
	
	if ($key == '' || $key == 'www' || $key == 'portal' || !isset($GLOBALS['portal_config']['project_settings'][$key])) {
		// $key = $GLOBALS['portal_config']['default_project'];
		$key = 'www';
	}
	
	return $key;

}

function portal_get_host_prefix() {

	$prefix = preg_replace('~^([^.]+).*~', '\1', $_SERVER['HTTP_HOST']);

	return $prefix;
	
}

function portal_get_project_info_by_key($key) {

	$query = 'SELECT * FROM portal_projects WHERE project_name = ?';
	
	$params = array($key);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {
		return $results[0];
	} else {
		return $results;
	}

}

function portal_convert_project_settings_to_local($project_key) {

	global $portal_config;

	foreach ($portal_config['project_settings'][$project_key] as $key => $value) {
	
		$portal_config[$key] = $value;
	
	}

}

function portal_log_member_access($member_id = '', $school_id = '', $type = '') {

	// this function adds a log record for a member's access

	$data = array();
	$data['member_id'] = $member_id;
	$data['school_id'] = $school_id;
	$data['access_time'] = date('Y-m-d H:i:s');
	$data['server_name'] = @$_SERVER['HTTP_HOST'];
	$data['referring_site'] = @$_SERVER['HTTP_REFERER'];
	$data['request_uri'] = $_SERVER['REQUEST_URI'];
	$data['ip_address'] = portal_get_member_ip_address();
	$data['request_type'] = $type;

	$log_id = mystery_insert_query('portal_access_log', $data, 'access_log_id', 'portal_dbh');

	return $log_id;

}

function portal_get_member_ip_address() {

	// This function gets the ip address for a member
	
	if (@$_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	
	return @$_SERVER['REMOTE_ADDR'];

}


function portal_parse_uri($uri) {

	// this function parses a URI and changes it into the $_PORTAL variable structure
	
	$_PORTAL = array();

	$url_parts = explode('/', portal_strip_query_string($uri));

	$_PORTAL['section'] = @$url_parts[1];
	$_PORTAL['activity'] = @$url_parts[2];
	$_PORTAL['action'] = @$url_parts[3];
	
	// check for additional parameters to translate

	$i = 4;

	while (isset($url_parts[$i]) && ($url_parts[$i] != '')) {

		$_PORTAL['params'][$url_parts[$i]] = $url_parts[($i+1)];

		$i += 2;

	}

	return $_PORTAL;

}


function portal_process_school_registration($request) {

	global $_PORTAL;
	
	$data = array();
	
	$data['school_name'] = $request['school_name'];
	$data['school_district'] = $request['school_district'];
	$data['school_address_1'] = $request['school_address_1'];
	$data['school_address_2'] = $request['school_address_2'];
	$data['school_city'] = $request['school_city'];
	$data['school_state'] = $request['school_state'];
	$data['school_zip'] = $request['school_zip'];
	$data['school_country'] = 'United States';
	$data['creation_date'] = date('Y-m-d H:i:s');

	$school_id = mystery_insert_query('portal_schools', $data, 'school_id', 'portal_dbh');
	
	return $school_id;

}

function portal_process_teacher_registration($request, $type = 'teacher') {

	// this function will register a new teacher

	global $_PORTAL;
	
	// if we don't have a school id, add a new school
	
	if ($request['school_id'] == '') {
	
		$_PORTAL['errors'][] = 'You must specify a School.';
		
		return 0;
	
	}
		
	if ($request['school_id'] == 'other') {
	
		$request['school_id'] = portal_process_school_registration($request);
	
	}
	
	// Now set up the member in the portal
	
	$request['type'] = $type;
	$request['member_interface'] = 6;

	$member_id = portal_process_member_registration($request);
		
	return $member_id;

}

function portal_delete_class($class_id) {
	
	// this function deletes a class and it's associated data
	
	$query = 'DELETE FROM portal_classes WHERE class_id = ?';
	$params = array($class_id);
	$status = mystery_delete_query($query, $params, 'portal_dbh');
	
	$query = 'DELETE FROM portal_class_activities WHERE class_id = ?';
	$params = array($class_id);
	$status = mystery_delete_query($query, $params, 'portal_dbh');
	
	$query = 'DELETE FROM portal_class_students WHERE class_id = ?';
	$params = array($class_id);
	$status = mystery_delete_query($query, $params, 'portal_dbh');

	$query = 'DELETE FROM portal_class_words WHERE class_id = ?';
	$params = array($class_id);
	$status = mystery_delete_query($query, $params, 'portal_dbh');

}

function portal_delete_member($member_id) {
	
	// this function deletes a class and it's associated data
	
	$query = 'DELETE FROM portal_members WHERE member_id = ?';
	$params = array($member_id);
	$status = mystery_delete_query($query, $params, 'portal_dbh');
	
	$query = 'DELETE FROM portal_class_students WHERE member_id = ?';
	$params = array($member_id);
	$status = mystery_delete_query($query, $params, 'portal_dbh');

}


function portal_get_class_word($class_id) {

	$query = 'SELECT class_word FROM portal_class_words WHERE class_id = ?';
	
	$params = array($class_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) == 0) {
	
		return false;
	
	} else {
		
		return $results[0]['class_word'];
	
	}

}

function portal_check_class_word($class_word) {

	$query = 'SELECT class_id FROM portal_class_words WHERE class_word = ?';
	
	$params = array($class_word);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	$n = count($results);
	
	if ($n == 0) {
	
		return false;
	
	} else {
	
		if ($n > 1) {
			trigger_error('two classes have the same class word: ' . $class_word, E_USER_WARNING);
		}
	
		return $results[0]['class_id'];
	
	}

}

function portal_set_class_word($class_id, $class_word) {

	// delete a class word for this class
	
	$query = 'DELETE FROM portal_class_words WHERE class_id = ?';
	$params = array($class_id);
	$status = mystery_delete_query($query, $params, 'portal_dbh');
	
	// add the new word

	$data = array();
	$data['class_id'] = $class_id;
	$data['class_word'] = $class_word;

	$id = mystery_insert_query('portal_class_words', $data, 'class_word_id', 'portal_dbh');

}


function portal_process_student_registration($request) {

	global $_PORTAL;
		
	if (isset($request['class_id'])) {
	
		$request['member_interface'] = $request['interface'];
	
	} else {
	

		// check that the class_key is valid

		$query = 'SELECT pcw.class_id, class_school, member_interface FROM portal_class_words AS pcw LEFT JOIN portal_classes AS pc ON pcw.class_id=pc.class_id LEFT JOIN portal_members AS pm ON pc.class_teacher=pm.member_id WHERE class_word = ?';
		$params = array($request['class_word']);
		
		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		if (count($results) == 0) {
	
			$_PORTAL['errors'][] = 'Your Sign-up Word is not correct.  Please double check with your teacher.';
	
			if (preg_match('~(shit|piss|bitch|fuck|cunt|cock|suck|tits|crap|damn|merde|mierda|puta|cabron|pendejo)~i', $request['class_word'])) {
				$_PORTAL['errors'][] = 'You also have a potty mouth…  Please wash it out with soap!';
			}
			
			return 0;
	
		} else {
		
			$request['class_id'] = $results[0]['class_id'];
			$request['school_id'] = $results[0]['class_school'];
			$request['member_interface'] = $results[0]['member_interface'];

		}
	
	}

	$request['type'] = 'student';
	
	// add the student as a member
	
	$member_id = portal_process_member_registration($request);
	
	if ($member_id > 0) {
	
		// add the student to the selected class

		$record_id = portal_add_member_to_class($member_id, $request['class_id']);

	}
	
	return $member_id;
	
}

function portal_add_member_to_class($member_id, $class_id) {

	// for now, it's one class per student, so delete their other roles
	
	$query = 'DELETE FROM portal_class_students WHERE member_id = ?';
	$params = array($member_id);
	
	$status = mystery_delete_query($query, $params, 'portal_dbh');
	
	$data = array();
	
	$data['class_id'] = $class_id;
	$data['member_id'] = $member_id;
	$data['creation_date'] = date('Y-m-d H:i:s');
	
	$record_id = mystery_insert_query('portal_class_students', $data, 'class_student_id', 'portal_dbh');
	
	return $record_id;

}

function portal_process_member_registration($request) {

	// this function will register a member in the portal
	
	global $_PORTAL;

	// look for an email address - if not specified, student or old school teacher, generate a unique one
	
	if (trim(@$request['email']) == '') {
		$request['email'] = 'no-email-' . uniqid(md5(rand()), true);
	}
	
	if ($request['first_name'] == '') {
		$_PORTAL['errors'][] = 'You must specify a First Name.';
	}
	
	if ($request['last_name'] == '') {
		$_PORTAL['errors'][] = 'You must specify a First Name.';
	}
	
	if ($request['password'] == '') {
		$_PORTAL['errors'][] = 'You must specify a Password.';
	}
	
	if (strlen($request['password']) < 4 || strlen($request['password']) > 40) {
		$_PORTAL['errors'][] = 'Your password must be between 4 and 40 characters long.';
	}
	
	if (count($_PORTAL['errors']) > 0) {
		return 0;
	}
	
	// Check to see if this member is already in our system. If so, bail out.

	$query = 'SELECT member_id FROM portal_members WHERE member_email = ?';
	$params = array(@$request['email']);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {
	
		$_PORTAL['errors'][] = 'A person has already registered in the system with that email address. Please try signing in instead.  If you don\'t know your password, please contact <a href="mailto:webmaster@concord.org">webmaster@concord.org</a>.';
		
		return 0;

	} else {
	
		// get a unique username for this user
		
		$request['username'] = portal_get_unique_username($request['first_name'], $request['last_name'], $request['email']);
		
		$_REQUEST['username'] = $request['username']; // allows us to display it to the user
		
		$fixed_username = strtolower($request['username']);
		$fixed_password_ue = strtolower($request['password']);
		$fixed_password = md5($fixed_password_ue);
		
		// maybe we need to add the portal record first so we can set up the username
		
		$cc_member_id = portal_get_cc_member_id($request['first_name'], $request['last_name'], $request['email'], $request['username'], $request['password']);
		
		// create the member in the ITSI DIY by creating a session
		
		portal_setup_diy_session($fixed_username, $fixed_password_ue);
		
		$diy_member_id = portal_get_diy_member_id_from_db($fixed_username);
		
		portal_update_diy_member_info($diy_member_id, $request['first_name'], $request['last_name'], $request['email'], $request['member_interface']);

		$sds_member_id = portal_get_sds_member_id_from_db($diy_member_id);
		
		// add the member to the portal database
		
		$data = array();
		
		$data['member_username'] = $fixed_username;
		$data['member_password'] = $fixed_password;
		$data['member_password_ue'] = $request['password'];
		$data['member_first_name'] = $request['first_name'];
		$data['member_last_name'] = $request['last_name'];
		$data['member_email'] = $request['email'];
		$data['member_type'] = $request['type'];
		$data['member_school'] = $request['school_id'];
		$data['member_grade'] = @$request['grade_level'];
		$data['cc_member_id'] = $cc_member_id;
		$data['diy_member_id'] = $diy_member_id;
		$data['sds_member_id'] = $sds_member_id;
		$data['member_interface'] = $request['member_interface'];
		$data['member_source'] = @$request['source'];
		$data['creation_date'] = date('Y-m-d H:i:s');

		$member_id = mystery_insert_query('portal_members', $data, 'member_id', 'portal_dbh');
		
		if ($member_id < 1) {
			$_PORTAL['errors'][] = 'Could not create new member.  Please contact <a href="mailto:webmaster@concord.org">webmaster@concord.org</a>';
		}

	}
		
	return $member_id;	

}

function portal_get_unique_username($first_name, $last_name, $email) {
	
	// this function determines a unique username for a user given their first and last name
	
	$first_name = preg_replace('~[^a-zA-Z]~', '', $first_name);
	$last_name = preg_replace('~[^a-zA-Z]~', '', $last_name);

	$temp = $first_name . substr($last_name, 0, 1);
	
	$new_username = '';

	/* We used to look at the portal members table but that doesn't help with conflicts from members 
	$query = 'SELECT member_username FROM portal_members WHERE member_username LIKE ? ORDER BY member_username';
	$params = array($temp . '%');
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	*/
	
	$query = 'SELECT user_username FROM mystri_users WHERE user_email = ?';
	$params = array($email);
	
	$results = mystery_select_query($query, $params, 'sunflower_dbh');
	
	if (count($results) > 0) {
	
		// return the existing username

		$new_username = $results[0]['user_username'];

	} else {
	
		// look only for the ones that are the usernames suffixed with numbers to avoid accidental matches.
		// case in point, danielc1 looked for danielc* then found danielcalder@mac.com and gave an error
		
		// added the $ because for a user named evan stern, we kept getting evans1 even though it already existed
		// mysql (sunflower)> SELECT user_username FROM mystri_users WHERE user_username = 'evans' OR user_username REGEXP 'evans[0-9]+' ORDER BY user_username;
		// | user_username    |
		// +------------------+
		// | evans1           | 
		// | revans1@aisd.net | 
		//
		// mysql (sunflower)> SELECT user_username FROM mystri_users WHERE user_username = 'evans' OR user_username REGEXP 'evans[0-9]+$' ORDER BY user_username;
		// | user_username |
		// +---------------+
		// | evans1        | 

	
		$query = 'SELECT user_username FROM mystri_users WHERE user_username = ? OR user_username REGEXP ? ORDER BY  ORDER BY ROUND(RIGHT(user_username,LENGTH(user_username)-LENGTH(?)));';
		$params = array($temp, $temp . '[0-9]+$', $temp);
		
		$results = mystery_select_query($query, $params, 'sunflower_dbh');
		
		//mystery_debug_query('sunflower_dbh'); exit;


		if (count($results) > 0) {
			
			$last_index = count($results) - 1;
		
			$last_username = $results[$last_index]['user_username'];
			
			$last_username_number = str_replace(strtolower($temp), '', $last_username);
			
			if ($last_username_number == '') {
				$last_username_number = 1;
			}
			
			$new_username_number = $last_username_number + 1;
			
			$new_username = $temp . $new_username_number;
			
		
		} else {
		
			$new_username = $temp;
		
		}
	
	}
	
	return $new_username;

}

function portal_update_cc_member_info($cc_member_id, $username, $password, $first_name, $last_name, $email) {
		
	$data = array();
	
	$data['user_username'] = strtolower($username);
	$data['user_first_name'] = $first_name;
	$data['user_last_name'] = $last_name;
	$data['user_email'] = $email;

	if ($password != '') {
		$data['user_password'] = md5(strtolower($password));
	}

	$status = mystery_update_query('mystri_users', $data, 'user_id', $cc_member_id, 'sunflower_dbh');
	
	return $status;

}

function portal_get_cc_member_id($first_name, $last_name, $email_address, $username, $password) {

	// Check for this user's record in the cc members database
	
	$query = 'SELECT user_id FROM mystri_users WHERE user_email = ?';
	$params = array($email_address);
	
	$results = mystery_select_query($query, $params, 'sunflower_dbh');
	
	if (count($results) > 0) {
	
		// member exists in cc_members, get their ID

		$cc_member_id = $results[0]['user_id'];
		
		// Update member info username/password so the account will work here and in DIY
		
		portal_update_cc_member_info($cc_member_id, $username, $password, $first_name, $last_name, $email_address);
	
	} else {
	
		// member doesn't exist in cc_members, add them and get the ID

		$data = array();
		
		$data['user_email'] = $email_address;
		$data['user_username'] = strtolower($username);
		$data['user_password'] = md5(strtolower($password));
		$data['user_first_name'] = $first_name;
		$data['user_last_name'] = $last_name;
	
		$cc_member_id = mystery_insert_query('mystri_users', $data, 'user_id', 'sunflower_dbh');
	
	}
	
	return $cc_member_id;

}

function portal_update_diy_member_info($diy_member_id, $first_name, $last_name, $email, $interface_id) {

	//portal_update_diy_member_info_using_db($diy_member_id, $first_name, $last_name, $email, $interface_id);

	//return;

	$data = array();
	
	$data['user[first_name]'] = $first_name;
	$data['user[last_name]'] = $last_name;
	$data['user[email]'] = $email . '@concord.org';
	$data['user[vendor_interface_id]'] = $interface_id;
	
	$path = '/users/' . $diy_member_id . '.xml';
	
	list($headers, $content) = portal_put_to_diy($data, $path);
	
	//mystery_print_r($headers, $content); exit;
	
	// curl -i -X PUT -d "user[vendor_interface_id]=4" http://admin_user:password@localhost:3000/users/2.xml
	
	// this wasn't working with updating student records

}

function portal_update_diy_member_info_using_db($diy_member_id, $first_name, $last_name, $email, $interface_id) {

	$data = array();
	
	$data['first_name'] = $first_name;
	$data['last_name'] = $last_name;
	$data['email'] = $email . '@concord.org';
	$data['vendor_interface_id'] = $interface_id;
	
	$status = '';
	
	$path = '/users/' . $diy_member_id . '.xml';
	
	list($headers, $content) = portal_put_to_diy($data, $path);
	
	//mystery_print_r($headers, $content); exit;
	
	// curl -i -X PUT -d "user[vendor_interface_id]=4" http://admin_user:password@localhost:3000/users/2.xml

}

function portal_setup_diy_session($username = '', $password = '') {

	// This function will send the user's email and password to the diy site and setup a session cookie for them
	
	global $portal_config;
	
	$path = '/session';
	
	$data = array();
	
	if ($username == '') {
		$username = @$_SESSION['portal']['member_username'];
	}
	
	if ($password == '') {
		$password = strtolower(@$_SESSION['portal']['member_password_ue']);
	}
	
	$data['login'] = $username;
	$data['password'] = $password;
	$data['commit'] = 'Log in';
	
	//trigger_error('POST DATA: ' . var_export($data, true));
	
	list($headers, $content) = portal_post_to_diy($data, $path);

	//trigger_error('HEADERS: ' . var_export($headers, true));

	//trigger_error('CONTENT: ' . var_export($content, true));

	preg_match('~' . $portal_config['diy_session_name'] . '=([^;]+);~', $headers, $matches);
	
	$diy_session_id = $matches[1];
	
	mystery_cookie($portal_config['diy_session_name'], $diy_session_id);

}


function portal_get_from_diy($path) {

	// this function allows one to get an authenticated resource from the DIY, typically an XML list
	
	global $portal_config;
	
	$response = '';

	$fp = fsockopen($portal_config['diy_server'], 80);
	
	if ($fp) {

		fputs($fp, "GET " . $portal_config['diy_server_path'] . $path . " HTTP/1.1\r\n");
		fputs($fp, "Host: " . $portal_config['diy_server'] . "\r\n");
		fputs($fp, "Authorization: Basic " . base64_encode($portal_config['diy_manager_user'] . ':' . $portal_config['diy_manager_password']) . "\r\n");
		//fputs($fp, "Accept: */*\r\n");
		//fputs($fp, "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n");
		fputs($fp, "Connection: close\r\n");
		//fputs($fp, "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n");
	
		while (!feof($fp)) {
			$response .= fgets($fp, 128);
		}
	
		fclose($fp);
	
	} else {
		
		trigger_error('Could not connect to diy server', E_USER_WARNING);
		
	}
	
	//mystery_print_r($response); exit;
	
	list($http_headers,$http_content) = explode("\r\n\r\n", $response);
	
	return $http_content;

}

function portal_put_to_diy($data, $path) {

	// this function takes an associative array of data and posts it to the DIY

	global $portal_config;
	
	$data_string = '';
	
	$data_string_parts = array();
	
	reset($data);
	
	while (list($key, $value) = each($data)) {
		$data_string_parts[] = $key . '=' . urlencode($value);
	}
	
	$data_string = implode('&', $data_string_parts);

	$response = '';

	$fp = fsockopen($portal_config['diy_server'], 80);
	
	if ($fp) {

		fputs($fp, "PUT " . $portal_config['diy_server_path'] . $path . " HTTP/1.1\r\n");
		fputs($fp, "Host: " . $portal_config['diy_server'] . "\r\n");
		fputs($fp, "Authorization: Basic " . base64_encode($portal_config['diy_manager_user'] . ':' . $portal_config['diy_manager_password']) . "\r\n");
		fputs($fp, "Accept: */*\r\n");
		fputs($fp, "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n");
		fputs($fp, "Connection: close\r\n");
		fputs($fp, "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n");
		fputs($fp, "Content-Length: " . strlen($data_string) . "\r\n\r\n");
		fputs($fp, $data_string);
	
		//mystery_print_r($data_string, $path);
	
		while (!feof($fp)) {
			$response .= fgets($fp, 128);
		}
	
		fclose($fp);
	
	} else {
		
		trigger_error('Could not connect to diy server', E_USER_WARNING);
		
	}
	
	//mystery_print_r($response); exit;
	
	list($http_headers,$http_content) = explode("\r\n\r\n", $response);
	
	return array($http_headers, $http_content);
	
}

function portal_post_to_diy($data, $path) {

	// this function takes an associative array of data and posts it to the DIY

	global $portal_config;
	
	$data_string = '';
	
	$data_string_parts = array();
	
	reset($data);
	
	while (list($key, $value) = each($data)) {
		$data_string_parts[] = urlencode($key) . '=' . urlencode($value);
	}
	
	$data_string = implode('&', $data_string_parts);
	
	//mystery_print_r($path);
	
	//mystery_print_r($data_string);

	$response = '';

	$fp = fsockopen($portal_config['diy_server'], 80);
	
	if ($fp) {

		fputs($fp, "POST " . $portal_config['diy_server_path'] . $path . " HTTP/1.1\r\n");
		fputs($fp, "Host: " . $portal_config['diy_server'] . "\r\n");
		fputs($fp, "Accept: */*\r\n");
		fputs($fp, "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n");
		fputs($fp, "Connection: close\r\n");
		fputs($fp, "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n");
		fputs($fp, "Content-Length: " . strlen($data_string) . "\r\n\r\n");
		fputs($fp, $data_string);
	
		//mystery_print_r($data_string); exit;
	
		while (!feof($fp)) {
			$response .= fgets($fp, 128);
		}
	
		fclose($fp);
	
	} else {
		
		trigger_error('Could not connect to diy server', E_USER_WARNING);
		
	}
	
	list($http_headers,$http_content) = explode("\r\n\r\n", $response);
	
	return array($http_headers, $http_content);
	
}

function portal_post_xml_to_diy($xml, $path) {

	// this function takes an xml snippit and posts it to the DIY

	global $portal_config;
	
	$response = '';

	$fp = fsockopen($portal_config['diy_server'], 80);

	if ($fp) {

		fputs($fp, "POST " . $portal_config['diy_server_path'] . $path . " HTTP/1.1\r\n");
		fputs($fp, "Host: " . $portal_config['diy_server'] . "\r\n");
		fputs($fp, "Accept: */*\r\n");
		fputs($fp, "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n");
		fputs($fp, "Connection: close\r\n");
		fputs($fp, "Content-Type: text/xml; charset=UTF-8\r\n");
		fputs($fp, "Content-Length: " . strlen($xml) . "\r\n\r\n");
		fputs($fp, $data_string);
	
		while (!feof($fp)) {
			$response .= fgets($fp, 128);
		}
	
		fclose($fp);
	
	} else {
		
		trigger_error('Could not connect to diy server', E_USER_WARNING);
		
	}
	
	//mystery_print_r($response);
	
	list($http_headers,$http_content) = explode("\r\n\r\n", $response);
	
	return array($http_headers, $http_content);

}

function portal_get_diy_member_id_from_db($member_username) {

	$query = 'SELECT id FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'users WHERE login = ?';
	
	$params = array($member_username);
	
	$results = mystery_select_query($query, $params, 'rails_dbh');
	
	if (count($results) > 0) {
		return $results[0]['id'];
	} else {
		return false;
	}

}

function portal_get_diy_member_id($member_id) {

	$member_info = portal_get_member_info($member_id);
	
	// PB: We can't cache the member id from the diy because they might be different diys for the same user and they don't
	// maintain id numbers between them...  so we need to lookup up the ID EVERY SINGLE TIME!!!!
	
	//if ($member_info['diy_member_id'] != 0 && $member_info['diy_member_id'] != '999999'  && $member_info['diy_member_id'] != '7777777') {
	//	return $member_info['diy_member_id'];
	//}

	$diy_member_id = portal_get_diy_member_id_from_db($member_info['member_username']);
	
	//if ($diy_member_id) {
	//
	//	// update this record (probably for the last time)
	//
	//	$data['diy_member_id'] = $diy_member_id;
	//	
	//	$status = mystery_update_query('portal_members', $data, 'member_id', $member_id, 'portal_dbh');
	//
	//}
	
	return $diy_member_id;

}

function portal_get_sds_member_id_from_db($diy_member_id) {

	$query = 'SELECT sds_sail_user_id FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'users WHERE id = ?';
	
	$params = array($diy_member_id);
	
	$results = mystery_select_query($query, $params, 'rails_dbh');
	
	if (count($results) > 0) {
		return $results[0]['sds_sail_user_id'];
	} else {
		return false;
	}

}

function portal_get_diy_activity_usage_from_db($member_id) {

	$member_diy_id = portal_get_diy_member_id($member_id);
	
	$query = 'SELECT runnable_id AS diy_id FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'learners WHERE user_id = ? AND runnable_type = ?';

	$params = array($member_diy_id, $GLOBALS['portal_config']['diy_runnable_type_name']);

	$results = mystery_select_query($query, $params, 'rails_dbh');
	
	$activities_used = array();
	
	for ($i = 0; $i < count($results); $i++) {
	
		$activities_used[] = $results[$i]['diy_id'];
	
	}
	
	return $activities_used;

}


function portal_get_diy_member_id_from_rest($first_name, $last_name, $email_address, $username, $password) {

	global $portal_config;
	
	$uri = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/user/create';
	
	$path = '/user/create';
	
	$host = $portal_config['diy_server'];
	
	$data = '
	<user>
		<email>' . $email_address . '</email>
		<login>' . strtolower($username) . '</login>
		<password>' . strtolower($password) . '</password>
		<firstname>' . $first_name . '</firstname>
		<lastname>' . $last_name . '</lastname>
	</user>
	';
	
	$fp = fsockopen($host, 80);
	fputs($fp, "POST " . $path . " HTTP/1.1\r\n");
	fputs($fp, "Host: " . $host . "\r\n");
	fputs($fp, "Accept: */*\r\n");
	fputs($fp, "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n");
	fputs($fp, "Connection: close\r\n");
	fputs($fp, "Content-Type: text/xml; charset=UTF-8\r\n");
	fputs($fp, "Content-Length: " . strlen($data) . "\r\n\r\n");
	fputs($fp, $data);
	
	//mystery_print_r($data);

	$response = '';

	while (!feof($fp)) {
		$response .= fgets($fp, 128);
	}

	fclose($fp);
	
	//mystery_print_r($response);
	
	list($http_headers,$http_content) = explode("\r\n\r\n", $response);
	
	// rather than properly parse the xml, we'll just do a quick regular expression
	// in the future, we could probably use MiniXML - http://minixml.psychogenic.com/overview.html
	
	preg_match('~>([0-9]+)</id>~', $http_content, $matches);
	
	$diy_member_id = $matches[1];
	
	if ($diy_member_id == '' || $diy_member_id == 0) {
		$diy_member_id = 999999;
	}
	
	return $diy_member_id;
	
}

function portal_get_diy_activities() {

	// This function get's all of the DIY activities in XML format then converts to a PHP array

	$activity_xml = portal_get_from_diy('/users/9/activities.xml');
	
	mystery_print_r($activity_xml);

}

function portal_icon($key, $title = '') {

	global $portal_config;
	
	$tag = '';
	
	if (!isset($portal_config['icons'][$key])) {
		$tag = ' - ';
	} else {
		if (preg_match('~MSIE~i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('~MSIE 7~i', $_SERVER['HTTP_USER_AGENT'])) {
			$img = $portal_config['ie-icons'][$key];
		} else {
			$img = $portal_config['icons'][$key];
		}
		$tag = '<img src="' . $img . '" alt="' . $key . '" title="' . $title . '">';
	}
	
	return $tag;

}

function portal_icon_link($icon, $url, $box_id, $title = '') {

	$link = '';
	
	$link .= '<a href="' . $url . '" onclick="select_box_link(\'' . $box_id . '\', \'' . $url . '\'); return false;" title="'. $title . '">' . portal_icon($icon, $title) . '</a>';
	
	return $link;
	
}

function portal_simple_icon_link($icon, $url, $title = '') {

	$link = '';
	
	$link .= '<a href="' . $url . '" title="'. $title . '">' . portal_icon($icon, $title) . '</a>';
	
	return $link;
	
}

function portal_get_class_students($class_id) {

	$query = 'SELECT * FROM portal_class_students AS pcs LEFT JOIN portal_members AS pm ON pcs.member_id=pm.member_id WHERE class_id = ? ORDER BY member_last_name, member_first_name';
	
	$params = array($class_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');

	return $results;

}


function portal_subscribe_class_to_activities($class_id, $old_activities, $new_activities) {

	$to_add = array_values(array_diff($new_activities, $old_activities));
	
	
	$to_delete = array_values(array_diff($old_activities, $new_activities));
	
	// mystery_print_r($to_add); mystery_print_r($to_delete); exit;
	
	// first delete the old ones
	
	if (count($to_delete) > 0) {
	
		$query = 'DELETE FROM portal_class_activities WHERE class_id = ? AND activity_id IN (' . implode(',', $to_delete) . ')';
		
		$params = array($class_id);
		
		$status = mystery_delete_query($query, $params, 'portal_dbh');
	
	}
	
	// now add the new ones
	
	for ($i = 0; $i < count($to_add); $i++) {
	
		$data = array();
		$data['class_id'] = $class_id;
		$data['activity_id'] = $to_add[$i];
		
		$id = mystery_insert_query('portal_class_activities', $data, 'class_activity_id', 'portal_dbh');
	
	}

}

function portal_subscribe_class_to_diy_activities($class_id, $old_activities, $new_activities) {
	
	global $_PORTAL;

	//mystery_print_r($old_activities); mystery_print_r($new_activities); exit;

	$to_add = array_values(array_diff($new_activities, $old_activities));
	
	$to_delete = array_values(array_diff($old_activities, $new_activities));
	
	//mystery_print_r($to_add); mystery_print_r($to_delete); exit;
	
	// first delete the old ones
	
	if (count($to_delete) > 0) {
	
		$query = 'DELETE FROM portal_class_diy_activities WHERE class_id = ? AND project_id = ? AND diy_activity_id IN (' . implode(',', $to_delete) . ')';
		
		$params = array($class_id, $_PORTAL['project_info']['project_id']);
		
		$status = mystery_delete_query($query, $params, 'portal_dbh');
	
	}
	
	// now add the new ones
	
	for ($i = 0; $i < count($to_add); $i++) {
	
		$data = array();
		$data['class_id'] = $class_id;
		$data['diy_activity_id'] = $to_add[$i];
		$data['project_id'] = $_PORTAL['project_info']['project_id'];
		
		$id = mystery_insert_query('portal_class_diy_activities', $data, 'class_diy_activity_id', 'portal_dbh');
		
	}

}


function portal_generate_uuid() {

	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		mt_rand( 0, 0x0fff ) | 0x4000,
		mt_rand( 0, 0x3fff ) | 0x8000,
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);

}

function portal_generate_error_page($errors = array()) {

	// this function generates an error page
	
	global $_PORTAL;
	
	$error_list = '';
	
	if (count($errors) > 0) {

		$error_list = '
		<ul>
			<li>' . implode("</li>\n<li>", $errors) . '</li>
		</ul>
		';
	
	}
	
	$page = '
	<h1>An error occurred</h1>
	
	<p>One or more errors occurred.</p>
	
	' . $error_list . '
	
	<p>Please <a href="javascript:history.back();"><strong>go back</strong></a> and try again or contact <a href="mailto:webmaster@concord.org">webmaster@concord.org</a>.</p>
	
	
	';

	return $page;

}


function portal_get_all_schools_info() {

	// this function gets information about all schools

	$query = 'SELECT * FROM portal_schools ORDER BY school_state, school_district, school_city, school_name';
	$params = array();
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	return $results;

}


function portal_generate_school_option_list() {

	// this function generates the pulldown list used on the teacher registration
	// page and the main portal administrator page.
	
	$option_list = '';
	
	$current_state = '';
	
	$schools = portal_get_all_schools_info();
	
	for ($i = 0; $i < count($schools); $i++) {
	
		if ($schools[$i]['school_state'] . ' - ' . $schools[$i]['school_district'] != $current_state) {
		
			if ($i > 0) {
				
				$option_list .= '
				</optgroup>
				';
			
			}
		
			$current_state = $schools[$i]['school_state'] . ' - ' . $schools[$i]['school_district'];

			$option_list .= '
			<optgroup label="' . $current_state . '">
			';
		
		}
		
		$option_list .= '
		<option value="' . $schools[$i]['school_id'] . '">' . $schools[$i]['school_city'] . ' — ' . $schools[$i]['school_name'] . '</option>
		';
	
	}

	$option_list .= '
	</optgroup>
	';
	
	return $option_list;

}





function portal_get_school_info($school_id) {

	// this function gets information about a school.

	$query = 'SELECT * FROM portal_schools WHERE school_id = ?';
	$params = array($school_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {
		return $results[0];
	} else {
		return $results;
	}
	
}

function portal_get_district_info($district_id) {

	// this function gets information about a school.

	$query = 'SELECT * FROM portal_districts WHERE district_id = ?';
	$params = array($district_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {
		return $results[0];
	} else {
		return $results;
	}
	
}

function portal_get_member_info($member_id) {

	// this function gets information about a member.  If $school_id is empty, it returns information for all schools

	$query = 'SELECT * FROM portal_members WHERE member_id = ?';
	$params = array($member_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {
		$results[0]['classes']['student'] = portal_get_student_classes($member_id);
		$results[0]['classes']['teacher'] = portal_get_teacher_classes($member_id);
		return $results[0];
	} else {
		return $results;
	}
	
}

function portal_get_student_classes($member_id) {

	$query = 'SELECT class_id FROM portal_class_students WHERE member_id = ?';
	
	$params = array($member_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	$classes = mystery_convert_results_to_simple_array($results, 'class_id');
	
	return $classes;

}

function portal_get_teacher_classes($member_id) {

	$query = 'SELECT class_id FROM portal_classes WHERE class_teacher = ?';
	
	$params = array($member_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	$classes = mystery_convert_results_to_simple_array($results, 'class_id');
	
	return $classes;

}

function portal_generate_teacher_list($school_id, $type = 'compact') {

	global $portal_config;

	$list = '';
	
	$teachers = portal_get_teachers($school_id);
	
	if (count($teachers) == 0) {
	
		$list = '<p><em>No teachers found</em></p>';
	
	} else {
	
		switch($type) {
		
			case 'compact':
			default:
			
				$list .= '
				<p><select name="teacher_id" id="teacher-id">
				';
			
				for ($i = 0; $i < count($teachers); $i++) {
				
					$list .= '
					<option value="' . $teachers[$i]['member_id'] . '">' . $teachers[$i]['member_last_name'] . ', ' . $teachers[$i]['member_first_name'] . '</option>
					';
				
				}
				
				$list .= '
				</select>
				' . portal_icon_link('setup', '/member/edit/', 'teacher-id', 'Edit this teacher') . '
				' . portal_icon_link('delete', '/member/delete/', 'teacher-id', 'Delete this teacher') . '
				</p>
				';
			
			break;
		
		}
	
	}
	
	return $list;

}

function portal_generate_member_list($type = 'compact') {

	// this function is only used in the admin pages

	global $portal_config;

	$list = '';
	
	$members = portal_get_members();
	
	if (count($members) == 0) {
	
		$list = '<p><em>No members found</em></p>';
	
	} else {
	
		switch($type) {
		
			case 'compact':
			default:
			
				$list .= '
				<p><select name="member_id" id="member-id">
				';
			
				for ($i = 0; $i < count($members); $i++) {
				
					$list .= '
					<option value="' . $members[$i]['member_id'] . '">' . $members[$i]['member_last_name'] . ', ' . $members[$i]['member_first_name'] . '</option>
					';
				
				}
				
				$list .= '
				</select>
				</p>
				';
			
			break;
		
		}
	
	}
	
	return $list;

}

function portal_generate_member_type_list($selected_type) {

	// this function is only used in the member editing page

	global $portal_config;

	$list = '';

	$list_parts = array();
	
	$student_selected = '';
	$teacher_selected = '';
	$admin_selected = '';
	$superuser_selected = '';
	
	switch ($selected_type) {
	
		case 'student':
			$student_selected = ' selected="selected"';
		break;
	
		case 'teacher':
			$teacher_selected = ' selected="selected"';
		break;
	
		case 'admin':
			$admin_selected = ' selected="selected"';
		break;
	
		case 'superuser':
			$superuser_selected = ' selected="selected"';
		break;
	
	}
	
	$list_parts[] = '<option value="student"' . $student_selected . '>Student</option>';
	$list_parts[] = '<option value="teacher"' . $teacher_selected . '>Teacher</option>';
	
	if ($_SESSION['portal']['member_type'] == 'admin' || $_SESSION['portal']['member_type'] == 'superuser') {
		$list_parts[] = '<option value="admin"' . $admin_selected . '>School Administrator</option>';
	} 
	
	if ($_SESSION['portal']['member_type'] == 'superuser') {
		$list_parts[] = '<option value="superuser"' . $superuser_selected . '>System Administrator</option>';
	}
	
	$list = '<select name="member_type">' . implode("\n", $list_parts) . '</select>';
	
	return $list;

}

function portal_generate_member_option_list() {

	// this function is only used in the admin pages

	global $portal_config;

	$list = '';
	
	$members = portal_get_members();
	
	if (count($members) > 0) {
	
		for ($i = 0; $i < count($members); $i++) {
		
			$role = '';
		
			//if (preg_match('~^no-email~', $members[$i]['member_email'])) {
			if ($members[$i]['member_type'] == 'student') {
				$role = ' - student';
			} else {
				$role = ' (' . $members[$i]['member_email'] . ') - ' . $members[$i]['member_type'];
			}
		
			$list .= '
			<option value="' . $members[$i]['member_id'] . '">' . $members[$i]['member_last_name'] . ', ' . $members[$i]['member_first_name'] . $role . '</option>
			';
		
		}
		
	}
	
	return $list;

}

function portal_generate_class_list($school_id, $teacher_id = '', $selected = '', $options = array()) {

	// FIXME - way too much overlap between this function and the next... they should be merged, probably using options

	global $portal_config;

	$list = '';

	if (!is_array($options)) {
		$options = array($options);
	}
	
	if (@$options['type'] == '') {
		$options['type'] = 'compact';
	}

	$conditions = array();
	$params = array();
	
	if ($teacher_id != '') {
		$conditions[] = 'class_teacher = ?';
		$params[] = $teacher_id;
	}

	$classes = portal_get_classes($school_id, $conditions, $params);
	
	if (count($classes) == 0) {
	
		$list .= '<p><em>No classes found</em></p>';
	
	} else {
	
		switch($options['type']) {
		
			case 'compact':
			default:
			
				$list .= '
				<p><select name="class_id" id="class-id">
				';
			
				for ($i = 0; $i < count($classes); $i++) {
				
					$s = '';
				
					if ($classes[$i]['class_id'] == $selected) {
						$s = ' selected="selected"';
					}
				
					if ($teacher_id == '') {
						$display = $classes[$i]['member_last_name'] . ', ' . $classes[$i]['member_first_name'] . ' — ' . $classes[$i]['class_name'];
					} else {
						$display = $classes[$i]['class_name'];
					}
				
					$list .= '
					<option value="' . $classes[$i]['class_id'] . '"' . $s . '>' . $display . '</option>
					';
				
				}
				
				$list .= '
				</select>
				
				' . portal_icon_link('preview', '/class/preview/', 'class-id', 'Preview this class as your students would see it') . '

				' . portal_icon_link('setup', '/class/edit/', 'class-id', 'Setup this class') . '

				' . portal_icon_link('copy', '/class/copy/', 'class-id', 'Make a copy of this class and its activities') . '

				' . portal_icon_link('report', '/class/report/', 'class-id', 'View a report on this class') . '

				' . portal_icon_link('list', '/class/roster/', 'class-id', 'View a class list') . '

				' . portal_icon_link('delete', '/class/delete/', 'class-id', 'Delete this class') . '

				</p>
				';
			
			break;
		
		}
	
	}
	
	return $list;


}

function portal_generate_class_select_list($school_id, $teacher_id = '', $selected = '') {

	global $portal_config;

	$list = '';

	$conditions = array();
	$params = array();
	
	if ($teacher_id != '') {
		$conditions[] = 'class_teacher = ?';
		$params[] = $teacher_id;
	}

	$classes = portal_get_classes($school_id, $conditions, $params);
	
	if (count($classes) == 0) {
	
		$list .= '<p><em>No classes found</em></p>';
	
	} else {
	
		$list .= '
		<p><label for="class">Class</label> <select name="class_id" id="class-id">
		';
	
		for ($i = 0; $i < count($classes); $i++) {
		
			$s = '';
		
			if ($classes[$i]['class_id'] == $selected) {
				$s = ' selected="selected"';
			}
		
			if ($teacher_id == '') {
				$display = $classes[$i]['member_last_name'] . ', ' . $classes[$i]['member_first_name'] . ' — ' . $classes[$i]['class_name'];
			} else {
				$display = $classes[$i]['class_name'];
			}
		
			$list .= '
			<option value="' . $classes[$i]['class_id'] . '"' . $s . '>' . $display . '</option>
			';
		
		}
		
		$list .= '
		</select></p>
		';
	
	}
	
	return $list;

}

function portal_generate_student_list($school_id, $teacher_id = '', $options = array()) {

	global $portal_config;

	$conditions = array();
	$params = array();
	
	if (!is_array($options)) {
		$options = array($options);
	}
	
	if (@$options['type'] == '') {
		$options['type'] = 'compact';
	}

	if ($teacher_id != '') {
		$conditions[] = 'class_teacher = ?';
		$params[] = $teacher_id;
	}

	$students = portal_get_students($school_id, $conditions, $params);
	
	$student_name_count = array();
	
	$list = '';
	
	if (count($students) == 0) {
	
		$list .= '<p><em>No students found</em></p>';
	
	} else {
	
		switch($options['type']) {
		
			case 'compact':
			default:
			
				$list .= '
				<p><select name="student_id" id="student-id">
				';
			
				for ($i = 0; $i < count($students); $i++) {
				
					$full_name = $students[$i]['member_last_name'] . ', ' . $students[$i]['member_first_name'];

					if (!isset($student_name_count[$full_name])) {
						$student_name_count[$full_name] = 0;
					}
					
					$student_name_count[$full_name]++;
				
					if ($student_name_count[$full_name] > 1) {
						$full_name .= ' (' . $student_name_count[$full_name] . ')';
					}
				
					$list .= '
					<option value="' . $students[$i]['member_id'] . '">' . $full_name . '</option>
					';
				
				}
				
				$list .= '
				</select>
				
				' . portal_icon_link('setup', '/member/edit/', 'student-id', 'Edit this student') . '

				' . portal_icon_link('report', '/member/report/', 'student-id', 'View a report about this student') . '

				' . portal_icon_link('delete', '/member/delete/', 'student-id', 'Delete this student') . '

				</p>
				';
			
			break;
		
		}
	
	}
	
	return $list;

}


function portal_get_teachers($school_id) {

	$query = 'SELECT * FROM portal_members WHERE member_school = ? AND member_type <> ? ORDER BY member_last_name, member_first_name';
	
	$params = array($school_id, 'student');
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	return $results;

}

function portal_get_members() {

	$query = 'SELECT * FROM portal_members ORDER BY member_last_name, member_first_name';
	
	$params = array();
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	return $results;

}

function portal_get_class_info($class_id) {

	global $_PORTAL;

	$class_info = array();

	$query = 'SELECT * FROM portal_classes WHERE class_id = ?';
	
	$params = array($class_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {

		$class_info = $results[0];

		// now get this classes activities (limited to the current project)
		
		$query = 'SELECT pca.activity_id FROM portal_class_activities AS pca LEFT JOIN portal_activities AS pa ON pca.activity_id=pa.activity_id LEFT JOIN portal_units AS pu ON pa.activity_unit=pu.unit_id WHERE pca.class_id = ? AND pu.unit_project = ?';
		
		$params = array($class_id, $_PORTAL['project_info']['project_id']);
		
		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$activities = array();
		
		for ($i = 0; $i < count($results); $i++) {
		
			$activities[] = $results[$i]['activity_id'];
		
		}
		
		$class_info['activities'] = $activities;
		
		// now get class custom diy activities
		
		$query = 'SELECT diy_activity_id FROM portal_class_diy_activities WHERE class_id = ? AND project_id = ?';
		
		$params = array($class_id, $_PORTAL['project_info']['project_id']);
		
		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$diy_activities = array();
		
		for ($i = 0; $i < count($results); $i++) {
		
			$diy_activities[] = $results[$i]['diy_activity_id'];
		
		}
		
		$class_info['diy_activities'] = $diy_activities;

		$class_info['class_word'] = portal_get_class_word($class_id);

	}
	
	return $class_info;

}

function portal_get_class_diy_activities($class_id) {

	global $_PORTAL;

	$diy_ids = array();

	// get the general diy activities
	
	$field = 'diy_identifier';
	
	if ($GLOBALS['portal_config']['diy_use_uuid'] == 'yes') {
		$field = 'diy_uuid';
	}

	$query = 'SELECT ' . $field . ' AS diy_identifier FROM portal_class_activities AS pca LEFT JOIN portal_activities AS pa ON pca.activity_id=pa.activity_id LEFT JOIN portal_units AS pu ON pa.activity_unit=pu.unit_id WHERE pca.class_id = ? AND pu.unit_project = ?';
	
	$params = array($class_id, $_PORTAL['project_info']['project_id']);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	for ($i = 0; $i < count($results); $i++) {
	
		$diy_ids[] = $results[$i]['diy_identifier'];
	
	}
	
	// get the diy and custom activities
	
	$query = 'SELECT diy_activity_id FROM portal_class_diy_activities WHERE class_id = ? AND project_id = ?';
	
	$params = array($class_id, $_PORTAL['project_info']['project_id']);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	for ($i = 0; $i < count($results); $i++) {
	
		$diy_ids[] = $results[$i]['diy_activity_id'];
	
	}
	
	if (count($diy_ids) == 0) {
		$diy_ids[] = 0;
	}
	
	// now get the name/id information from the diy
	
	$diy_field = 'id';
	
	if ($GLOBALS['portal_config']['diy_use_uuid'] == 'yes') {
		$diy_field = 'uuid';
	}
	
	$query = 'SELECT 
	ida.' . $diy_field . ' AS activity_id, 
	ida.name AS activity_name
	FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . $GLOBALS['portal_config']['diy_activities_name'] . ' AS ida
	WHERE ' . $diy_field . ' IN ("' . implode('","', $diy_ids) . '")
	ORDER BY activity_name
	';
	
	$params = array();
	
	$results = mystery_select_query($query, $params, 'rails_dbh');
		
	return $results;

}


function portal_get_classes($school_id, $conditions = array(), $params = array()) {

	$query = 'SELECT * FROM portal_classes AS pc LEFT JOIN portal_members AS pm ON pc.class_teacher=pm.member_id';
	
	$query_conditions = array();
	$query_params = array();
	
	$query_conditions[] = 'class_school = ?';
	$query_params[] = $school_id;
	
	for ($i = 0; $i < count($conditions); $i++) {
		$query_conditions[] = $conditions[$i];
	}
	
	for ($i = 0; $i < count($params); $i++) {
		$query_params[] = $params[$i];
	}
	
	if (count($query_conditions) > 0) {
		$query .= ' WHERE ' . implode(' AND ', $query_conditions);
	}
	
	$query .= ' ORDER BY member_last_name, member_first_name, class_name';
	
	$results = mystery_select_query($query, $query_params, 'portal_dbh');
	
	return $results;

}

function portal_get_students($school_id, $conditions = array(), $params = array()) {

	$query = 'SELECT pm.*, pc.class_teacher FROM portal_members AS pm LEFT JOIN portal_class_students AS pcs ON pm.member_id=pcs.member_id LEFT JOIN portal_classes AS pc ON pcs.class_id=pc.class_id';
	
	$query_conditions = array();
	$query_params = array();
	
	$query_conditions[] = 'member_school = ?';
	$query_conditions[] = 'member_type = ?';

	$query_params[] = $school_id;
	$query_params[] = 'student';
	
	for ($i = 0; $i < count($conditions); $i++) {
		$query_conditions[] = $conditions[$i];
	}
	
	for ($i = 0; $i < count($params); $i++) {
		$query_params[] = $params[$i];
	}
	
	if (count($query_conditions) > 0) {
	
		$query .= ' WHERE ' . implode(' AND ', $query_conditions);
	
	}
	
	$query .= ' ORDER BY member_last_name, member_first_name';
	
	$results = mystery_select_query($query, $query_params, 'portal_dbh');
	
	return $results;

}




function portal_is_supported_browser() {

	$ua = $_SERVER['HTTP_USER_AGENT'];

	if (preg_match('~MSIE~', $ua) && !preg_match('~7~', $ua)) {
	
		return false;
	
	}
	
	return true;

}
function portal_strip_query_string($url) {

	$url = preg_replace('~\?.*$~','', $url);
	
	return $url;

}


function portal_generate_image_src($school_id, $image_id, $extension, $option = '') {

	global $portal_config;
	
	$thumb = '';
	
	if ($option == 'thumbnail') {
		$thumb = 'thumb-';
	}
	
	$src = $portal_config['image_upload_web_path'] . '/' . $school_id . '/' . $thumb . $image_id . '.' . $extension;

	return $src;

}

function portal_get_file_extension($file_name, $file_type) {

	// this function determines which file extension to use for a given file name
	
	$ext = '';

	// types to extensions mapping
	
	// FIXME - add additional types for likely used items

	$map = array();
	$map['text/plain'] = 'txt';
	$map['text/html'] = 'html';
	$map['application/vnd.ms-word'] = 'doc';
	$map['application/pdf'] = 'pdf';
	$map['image/jpeg'] = 'jpg';
	$map['image/jpg'] = 'jpg';
	$map['image/gif'] = 'gif';
	$map['image/png'] = 'png';

	// First look for an extension on the file name.
	
	$pos = strrpos($file_name, '.');
	
	if ($pos !== false) {
		$ext = substr($file_name, $pos+1);
	}
	
	// If the filename extension isn't there, select from a standard list

	if ($ext == '') {
		$ext = @$map[$file_type];
	}
	
	// if the extension still isn't there, just return a default
	
	if ($ext == '') {
		$ext = 'unk';
	}
	
	return $ext;

}



function portal_sanitize_file_name($file_name, $suffix = '', $content_type = '') {

	// this function returns a sanitized file name with extension
	
	// the extension is based on the content type if specified, on the original file name if not, and by default .unk

	$extension = portal_get_file_extension($file_name, $content_type);
	
	$sanitized = strtolower($file_name);

	// remove extension from filename if there

	$sanitized = str_replace($extension, '', $sanitized);
	
	// remove non-alpha-numeric characters from filename
	
	$sanitized = preg_replace('~[^-_a-z0-9]~', '', $sanitized);
	
	// add suffix if applicable
	
	if ($suffix != '') {
		$sanitized = $sanitized . '-' . $suffix;
	}
	
	// add extension
	
	$sanitized = $sanitized . '.' . $extension;
	
	return $sanitized;

}


function portal_store_data_in_filesystem($filename, $data) {

	// This function puts the contents of $data into $filename

	// first we need to create the directory if applicable
	
	portal_create_directory(dirname($filename));
	
	// now we can write the file

	$fp = fopen($filename, 'wb');
	
	if ($fp) {
		fwrite($fp, $data);
		fclose($fp);
		return true;
	}

	return false;

}

function portal_store_file_in_filesystem($original_file, $new_location) {

	// This function copies $data_file into $filename

	// first we need to create the directory if applicable
	
	if (portal_create_directory(dirname($new_location))) {
	
		// now we can write the file
		
		if (copy($original_file, $new_location)) {
		
			return true;
		
		}
	
	}
	
	return false;

}

function portal_create_directory($directory_path, $mode = 0777) {

	// this function makes any non existant directories in the given path
	
	$old_umask = umask(0);

	if (is_dir($directory_path)) {
		return true;
	}
	
	$parent_directory_path = dirname($directory_path);
	
	if (!portal_create_directory($parent_directory_path, $mode)) {
		return false;
	} else {
		return mkdir($directory_path, $mode);
	}

	umask($oldumask);

}


// image resizing options


function portal_generate_cropped_image($file, $max_width, $max_height) {

	// this function will take an image and crop it to a certain width, using the center as the reference point
	
	global $errors, $portal_image_types;

	// this function generates a resized image file with a maximum width and height of the specified parameters
	
	list($width, $height, $type_int, $other_data) = GetImageSize($file);
	
	$type = $portal_image_types[$type_int];
	
	$resized_image_file = tempnam('/tmp','gl-img-resize');
		
	if (($width > $max_width || $height > $max_height)) {
		
		$width_ratio = $max_width / $width;
		$height_ratio = $max_height / $height;
	
		if ($width_ratio > $height_ratio) {
			// the width is bigger, ratio-wise, so we'll scale it
			$new_width = $width * $width_ratio; // 800*.25 = 200
			$new_height = $height * $width_ratio; //200*.25 = 50
		} else {
			$new_width = $width * $height_ratio; // 800*.5 = 400
			$new_height = $height * $height_ratio; //200*.5 = 100
		}
			
		if ($type == 'JPG') {
			$image_data = @imagecreatefromjpeg($file);
		} elseif ($type == 'PNG') {
			$image_data = @imagecreatefrompng($file);
		} elseif ($type == 'GIF') {
			$image_data = @imagecreatefromgif($file);
		} else {
			$errors[] = 'The system currently can only process JPG, GIF, or PNG images.';
		}
		
		if (count($errors) == 0) {
		
			$converted_image = imagecreatetruecolor($new_width, $new_height);
	
			imagecopyresampled($converted_image, $image_data, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
			imagedestroy($image_data);
			
			$start_x = floor(($new_width - $max_width)/2);
			
			$start_y = floor(($new_height - $max_height)/2);
			
			$converted_image2 = imagecreatetruecolor($max_width, $max_height);
	
			imagecopyresampled($converted_image2, $converted_image, 0, 0, $start_x, $start_y, $max_width, $max_height, $max_width, $max_height);
	
			imagejpeg($converted_image2, $resized_image_file, 80);
		
			imagedestroy($converted_image);
		}
		
	} else {
	
		copy($file, $resized_image_file);
			
	}
	
	return $resized_image_file;

}


function portal_generate_resized_image($file, $max_width, $max_height) {

	global $errors, $portal_image_types;

	// this function generates a resized image file with a maximum width and height of the specified parameters
	
	list($width, $height, $type_int, $other_data) = GetImageSize($file);
	
	$type = $portal_image_types[$type_int];
	
	$resized_image_file = tempnam('/tmp','gl-img-resize');
		
	if (($width > $max_width || $height > $max_height)) {
		
		$width_ratio = $max_width / $width;
		$height_ratio = $max_height / $height;
	
		if ($width_ratio < $height_ratio) {
			// the width is bigger, ratio-wise, so we'll scale it
			$new_width = $width * $width_ratio; // 800*.25 = 200
			$new_height = $height * $width_ratio; //200*.25 = 50
		} else {
			$new_width = $width * $height_ratio; // 800*.5 = 400
			$new_height = $height * $height_ratio; //200*.5 = 100
		}
			
		if ($type == 'JPG') {
			$image_data = @imagecreatefromjpeg($file);
		} elseif ($type == 'PNG') {
			$image_data = @imagecreatefrompng($file);
		} elseif ($type == 'GIF') {
			$image_data = @imagecreatefromgif($file);
		} else {
			$errors[] = 'The system currently can only process JPG, GIF, or PNG images.';
		}
		
		if (count($errors) == 0) {
		
			$converted_image = imagecreatetruecolor($new_width, $new_height);
	
			imagecopyresampled($converted_image, $image_data, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
			imagedestroy($image_data);
			
			imagejpeg($converted_image, $resized_image_file, 80);
		
		}
		
	} else {
	
		copy($file, $resized_image_file);
			
	}
	
	return $resized_image_file;

}


function portal_generate_notebox($message) {

	$notebox = '';
	
	if (trim($message) != '') {
	
		$notebox = '
		<div class="notebox">
		' . $message . '
		</div>
		';
	
	}
	
	return $notebox;

}

function portal_generate_commentbox($content) {

	static $current = '';
	
	$light = '#FDFDFD';
	$dark = '#ECECEC';

	if ($current == $dark) {
		$current = $light;
	} else {
		$current = $dark;
	}

	$s = ' style="background-color: ' . $current . '"';

	$box = '';
	
	$box = '
	<div class="roundedbox">
		
		<b class="b-top"><b class="b-b1"></b><b class="b-b2"' . $s . '></b><b class="b-b3"' . $s . '></b><b class="b-b4"' . $s . '></b></b>
		
		<div class="b-contentbox"' . $s . '>
		
		' . $content . '
		
		</div>
		
		<b class="b-bottom"><b class="b-b4"' . $s . '></b><b class="b-b3"' . $s . '></b><b class="b-b2"' . $s . '></b><b class="b-b1"></b></b>
		
	</div>
	';
	
	return $box;

}







// localization stuff

function portal_get_string($string) {

	// This file will load in the language file (if not already loaded), 
	// look for the right word, and return it if found.  If not found, it
	// will load the english file and look for the word there.  If still not
	// found it will return the '???????' string.
	
	global $portal_config;
	
	static $langs;
	
	$not_found = '?????????';
	
	if (!isset($langs[$portal_config['default_language']])) {
		@include 'gl/lang/strings.' . $portal_config['default_language'] . '.php';
		$langs[$portal_config['default_language']] = @$s;
	}
	
	if (!isset($langs[$portal_config['default_language']][$string])) {

		if (!isset($langs['en'])) {
			include 'gl/lang/strings.en.php';
			$langs['en'] = $s;
		}
		
		$langs[$portal_config['default_language']][$string] = @$langs['en'][$string];
		
		trigger_error('String "' . $string . '" not defined for language "' . $portal_config['default_language'] . '"');
		
	}
	
	return $langs[$portal_config['default_language']][$string];

}

function portal_generate_db_form_list($field_name, $field_values, $table_name, $table_value_field, $table_label_field, $type = 'list', $multiple = '', $alternative_query = '', $alternative_params = array(), $pre_options = array(), $max_width = 0) {

	// this function generates an HTML select list based on values in a database
	// parameters:
	//	$field_name - The name of the field in the HTML form
	//	$field_values - An array of values (or a single value) that should be auto-filled in the form
	//	$table_name - The name of the database table
	//	$table_value_field - The field in the database table that will be used for the value part of the list, usually the PK
	//	$table_label_field - The field (can be a CONCAT statement) that will be used in the displayed part of the list
	//	$type - list | box - Whether to use select lists or radio/checkboxes
	//	$multiple - '' | multiple - whether to allow multiple selections (automatically set if $field_values is an array
	//	$alternative_query - If a simple table/value_field/label_field isn't good enough, use this to define an alternate parameterized query with joins, etc.  Just make sure to alias the value ' AS value' and the label ' AS label'
	//	$alternative_params - An array of parameters to use for the query
	//	$pre_options - An array of items to place before the list, typically things like '<option value="">Select an option...</option>' or '<option value="">---------</option>'
	//   $max_width - The maximum number of characters to be displayed in the form.  Default (0) is all characters

	global $_MYSTERY, $_PORTAL;

	// conifguration
	$select_box_limit = 6; // max display length of a select box

	// function body
	$list = '';

	if ($alternative_query == '') {
		$query = 'SELECT DISTINCT ' . $table_value_field . ' AS value, ' . $table_label_field . ' AS label FROM ' . $table_name . ' ORDER BY label ASC';
		$params = array();
	} else {
		$query = $alternative_query;
		$params = $alternative_params;
	}

	$results = mystery_select_query($query, $params, 'portal_dbh');

	// we'll override the multiple argument if multiple values are specified

	if (is_array($field_values) && count($field_values) > 1) {
		$multiple = 'multiple';
	}

	if ($multiple == 'multiple') {
		// we'll make the sent variable into an array
		if (!preg_match('~\[\]$~', $field_name)) {
			$field_name = $field_name . '[]';
		}
	}

	// now we'll convert $field_values to an array if not already done so

	if (!is_array($field_values)) {
		$field_values = array($field_values);
	}

	if ($type == 'list') {

		if ($multiple == 'multiple') {

			if (count($results) > $select_box_limit) {
				$size = ' size="' . $select_box_limit . '"';
			} else {
				$size = '';
			}

			$list .= '
			<select name="' . $field_name . '" id="' . str_replace('_','-',$field_name) . '"' . $size . ' multiple="multiple">
			';

		} else {

			$list .= '
			<select name="' . $field_name . '" id="' . str_replace('_','-',$field_name) . '">
			';

		}

		if (count($pre_options) > 0) {

			for ($i = 0; $i < count($pre_options); $i++) {

				$list .= $pre_options[$i];

			}

		}

		for ($i = 0; $i < count($results); $i++) {

			$selected = '';

			if (in_array($results[$i]['value'], $field_values)) {
				$selected = ' selected';

			}

			$label = $results[$i]['label'];
			
			if ($max_width > 0 && strlen($label) > $max_width) {
				$label = substr($label, 0, ($max_width - 3)) . '…';
			}
		
			$list .= '
			<option value="' . $results[$i]['value'] . '"' . $selected . '>' . $label . '</option>
			';

		}

		$list .= '</select>';

	} else {

		// $type == 'box'

		if ($multiple == 'multiple') {
			$box_type = 'checkbox';
		} else {
			$box_type = 'radio';
		}

		for ($i = 0; $i < count($results); $i++) {

			$checked = '';

			if (in_array($results[$i]['value'], $field_values)) {
				$checked = ' checked';

			}

			$label = $results[$i]['label'];
			
			if ($max_width > 0 && strlen($label) > $max_width) {
				$label = substr($label, 0, ($max_width - 3)) . '…';
			}
		
			$list .= '
			<label><input name="' . $field_name . '" type="' . $box_type . '" value="' . $results[$i]['value'] . '"' . $checked . '> ' . $label . '</label><br>
			';

		}
	}

	return $list;

}

function portal_generate_interface_list($interface_id = 0) {

	global $portal_config;

	$list = '';

	$list .= '
	<select name="interface" id="interface">
	';
	
	reset($portal_config['interfaces']);
	
	while (list($key, $value) = each($portal_config['interfaces'])) {
	
		$selected = '';
		
		if ($key == $interface_id) {
			$selected = ' selected="selected"';
		}
		
		$list .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
	
	}
	
	$list .= '
	</select>
	';

	return $list;

}

function portal_debug_query() {

	// this function will display the last exectued query on the lhh database connection

	mystery_debug_query('portal_dbh');

}


function portal_generate_user_info_box() {

	$box = '';
	
	global $portal_config;
	
	if (@$_SESSION['is_logged_in'] == 'yes') {
	
		$box .= '
		<div class="user-info-box">
		Welcome, <strong>' . $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name'] . '</strong><br>
		<a href="/signout/">Sign out?</a>
		<br><br>
		<strong>Probe Interface:</strong><br>
		<a href="/member/edit/' . $_SESSION['portal']['member_id'] . '/" title="Click to change interface">' . $portal_config['interfaces'][$_SESSION['portal']['member_interface']] . '</a>
		</div>
		';
	
	}
	
	return $box;

}

function portal_generate_icon_legend() {

	$legend = '';
	
	$icon_parts = array();
	
	if (@$_SESSION['is_logged_in'] == 'yes') {
	
		$icon_parts[] = '<tr><td>' . portal_icon('add') . '</td><td>Add</td></tr>';
		
		if (in_array('copy', $GLOBALS['portal_config']['available_actions'])) {
			$icon_parts[] = '<tr><td>' . portal_icon('copy') . '</td><td>Copy</td></tr>';
		}
		
		if (in_array('edit', $GLOBALS['portal_config']['available_actions'])) {
			$icon_parts[] = '<tr><td>' . portal_icon('setup') . '</td><td>Edit/Setup</td></tr>';
		}
		
		if (@$_SESSION['portal']['member_type'] != 'student') {
		
			$icon_parts[] = '<tr><td>' . portal_icon('delete') . '</td><td>Delete</td></tr>';
			$icon_parts[] = '<tr><td>' . portal_icon('list') . '</td><td>View class list</td></tr>';
			if (in_array('report', $GLOBALS['portal_config']['available_actions'])) {
				$icon_parts[] = '<tr><td>' . portal_icon('report') . '</td><td>View student data</td></tr>';
			}
			
		}
	
	}
	
	if (in_array('info', $GLOBALS['portal_config']['available_actions'])) {
		$icon_parts[] = '<tr><td>' . portal_icon('info') . '</td><td>Get info</td></tr>';
	}
	
	if (in_array('run', $GLOBALS['portal_config']['available_actions'])) {
		$icon_parts[] = '<tr><td>' . portal_icon('run') . '</td><td>Run  activity</td></tr>';
	}
	
	if (in_array('try', $GLOBALS['portal_config']['available_actions'])) {
		$icon_parts[] = '<tr><td>' . portal_icon('try') . '</td><td>Run activity but don\'t save data</td></tr>';
	}

	if (in_array('preview', $GLOBALS['portal_config']['available_actions'])) {
		$icon_parts[] = '<tr><td>' . portal_icon('preview') . '</td><td>Preview activity in browser</td></tr>';
	}
	

	$legend .= '
	<div class="icon-legend-box">

	<p><span id="icon-legend-link">Icon Legend</span></p>

	<div id="icon-legend">

	<table>
	' . implode("\n", $icon_parts) . '
	</table>
	
	</div>
	
	</div>
	
	<script type="text/javascript">

		function toggleLegendDisplay() {
		
			var link = document.getElementById("icon-legend-link");

			var legend = document.getElementById("icon-legend");
		
			if (legend.style.display == "none") {
				link.innerHTML = "Icon Legend (-)";
				legend.style.display = "block";
			} else {
				link.innerHTML = "Icon Legend (+)";
				legend.style.display = "none";
			}
			
		}
		
		$(document).ready(
			function() { 
				
				var link = document.getElementById("icon-legend-link");
				link.onclick = function () {
					toggleLegendDisplay();
				}
				link.style.cursor="pointer";
				
				toggleLegendDisplay();
			}
		);

	</script>
	';

	return $legend;

}


function portal_generate_user_navigation($options = array()) {

	global $_PORTAL, $portal_config;

	$nav = '';
	
	$nav_items = array();
	$small_nav_items = array();

	if (@$options['ignore-home'] != 'yes') {
	
		if ($_PORTAL['section'] == '' && $_PORTAL['activity'] == '') {
			$nav_items[] = '<li><strong>Home</strong></li>';
		} else {
			$nav_items[] = '<li><a href="/">Home</a></li>';
		}
	
	}

	if (@$_SESSION['is_logged_in'] == 'yes') {
	
		if ($_SESSION['portal']['member_type'] == 'student') {
		
		
			if ($_PORTAL['section'] == 'member' && $_PORTAL['activity'] == 'info') {
				$nav_items[] = '<li><strong>My Info</strong></li>';
			} else {
				$nav_items[] = '<li><a href="/member/info/">My Info</a></li>';
			}
		
		}
		
		if ($_SESSION['portal']['member_type'] != 'student' || $GLOBALS['portal_config']['show_activities_link_to_students'] == 'yes') {
			
			if ($_PORTAL['section'] == 'activity' && $_PORTAL['activity'] == 'create') {
				$nav_items[] = '<li><strong>' . $portal_config['activities_navigation_word'] . '</strong></li>';
			} else {
				$nav_items[] = '<li><a href="/activities/">' . $portal_config['activities_navigation_word'] . '</a></li>';
			}
		
		}
	
		if ($_SESSION['portal']['taking_course']) {
		
			if ($_PORTAL['section'] == 'course' && $_PORTAL['activity'] == '') {
				$nav_items[] = '<li><strong>Course</strong></li>';
			} else {
				$nav_items[] = '<li><a href="/course/">Course</a></li>';
			}
		
		}
	
		if ($_SESSION['portal']['member_type'] == 'admin' || $_SESSION['portal']['member_type'] == 'superuser') {
		
			if ($_PORTAL['section'] == 'admin' && $_PORTAL['activity'] == '') {
				$nav_items[] = '<li><strong>Admin</strong></li>';
			} else {
				$nav_items[] = '<li><a href="/admin/">Admin</a></li>';
			}
		
		}
	
		if ($_SESSION['portal']['member_type'] == 'superuser') {
		
			if ($_PORTAL['section'] == 'usage' && $_PORTAL['activity'] == '') {
				$nav_items[] = '<li><strong>Usage</strong></li>';
			} else {
				$nav_items[] = '<li><a href="/usage/">Usage</a></li>';
			}

			if ($_PORTAL['section'] == 'switch' && $_PORTAL['activity'] == '') {
				$nav_items[] = '<li><strong>Switch…</strong></li>';
			} else {
				$nav_items[] = '<li><a href="/switch/">Switch…</a></li>';
			}
		
		}
	
	} else {
	
		if ($_PORTAL['section'] == 'preview' && $_PORTAL['activity'] == '') {
			$nav_items[] = '<li><strong>' . $portal_config['activities_navigation_word'] . '</strong></li>';
		} else {
			$nav_items[] = '<li><a href="/preview/">' . $portal_config['activities_navigation_word'] . '</a></li>';
		}
	
	}
	
	if ($_PORTAL['section'] == 'about' && $_PORTAL['activity'] == '') {
		$nav_items[] = '<li><strong>About</strong></li>';
	} else {
		$nav_items[] = '<li><a href="/about/">About</a></li>';
	}

	if ($_PORTAL['section'] == 'help' && $_PORTAL['activity'] == '') {
		$nav_items[] = '<li><strong>Getting Started</strong></li>';
	} else {
		$nav_items[] = '<li><a href="/help/">Getting Started</a></li>';
	}
	
	if ($_PORTAL['section'] == 'support' && $_PORTAL['activity'] == '') {
		$nav_items[] = '<li><strong>Support</strong></li>';
	} else {
		$nav_items[] = '<li><a href="/support/">Support</a></li>';
	}
	
	for ($i = 0; $i < count(@$portal_config['extra_navigation_items']); $i++) {
	
		$label = $portal_config['extra_navigation_items'][$i]['label'];
		$value = $portal_config['extra_navigation_items'][$i]['value'];
		$deny = $portal_config['extra_navigation_items'][$i]['deny'];

		if (@$_SESSION['portal']['member_type'] != $deny && @$_SESSION['portal']['member_type'] != '') {
			$nav_items[] = '<li><a href="' . $value . '">' . $label . '</a></li>';
		}
	
	}
	

	if (count($nav_items) > 0) {

		$nav .= '
		<div class="user-navigation">
			
			<ul>
				' . implode("\n", $nav_items) . '
			</ul>
		
		</div>
		';
	
	}

	return $nav;

}

function portal_generate_technical_notes_section() {

	$note = '
	<h2>Technical Notes</h2>

	<h3>Flash Support</h3>
	
	<p><a href="http://jnlp.concord.org/dev/mozswing/mozswing.jnlp">Install Embedded Flash Support</a></p>
	
	<p><strong>Note:</strong> You may need to install <a href="http://www.mozilla.com/firefox/">Firefox</a> and the <a href="http://www.adobe.com/go/getflashplayer">Flash Player</a> if it is not already on your
	system.</p>
	
	<h3>Mac OS X Web Start Fix</h3>
	
	<p>If you are using MacOS 10.4 or later, you will almost certainly need to <a href="http://confluence.concord.org/display/CCTR/How+to+fix+the+WebStart+bug"><strong>fix a Java Web Start bug</strong></a>. You will need to follow the steps on that page once for each computer on which you run our activities, and additionally each time that java is updated.</p>
	';
	
	return $note;

}


function portal_web_output_filter($variable) {

	// this function takes a variable and recursively encodes entities and changes
	// linebreaks to <BR> tags.  This cleanses the variable for display on the web

	if (is_array($variable)) {

		reset($variable);

		while (list($key, $value) = each($variable)) {

			$variable[$key] = portal_web_output_filter($value);

		}

	} else {

		//$variable = str_replace('&','&amp;', $variable);
		//$variable = str_replace('&amp;&amp;','&amp;', $variable);
		$variable = nl2br(htmlspecialchars($variable));

	}

	return $variable;

}

function portal_web_output_filter_simple($variable) {

	// this function takes a variable and removes the most basic kinds of attack
	// vectors.  This should be used on rich text only. Eventually, we could look
	// at doing a more thorough check, something like:
	// http://cyberai.com/inputfilter/blacklist.php  -or-
	// http://hp.jpsband.org/
	

	$bad = array('~</?script~i','~document.cookie~i','~on[a-z]+=~i');

	if (is_array($variable)) {

		reset($variable);

		while (list($key, $value) = each($variable)) {

			$variable[$key] = portal_web_output_filter_simple($value);

		}

	} else {

		$variable = preg_replace($bad, '', $variable);

	}

	return $variable;

}

function portal_generate_student_activity_list($student_id, $class_id, $used_activities = array()) {
	
	global $_PORTAL;
	
	$query = '
	SELECT * FROM
	portal_class_activities AS pca 
	LEFT JOIN portal_activities AS pa
	ON pca.activity_id=pa.activity_id
	LEFT JOIN portal_units AS pu
	ON pa.activity_unit=pu.unit_id
	WHERE pca.class_id = ? AND pu.unit_project = ?
	ORDER BY unit_order, activity_order
	';
	
	$params = array($class_id, $_PORTAL['project_info']['project_id']);
	
	$activities = mystery_select_query($query, $params, 'portal_dbh');
	
	//portal_debug_query();
	
	$current_unit = '';
	
	$list = '';
	
	for ($i = 0; $i < count($activities); $i++) {
	
		if ($current_unit != $activities[$i]['unit_name']) {
		
			$current_unit = $activities[$i]['unit_name'];
			
			$list .= '<h2 style="margin-top: 1.5em;">' . $current_unit . '</h2>';
		
		}
		
		$activity_options = '';
		$activity_used = '';

		if ($activities[$i]['diy_identifier'] != '') {
		
			$interface_id = @$_SESSION['portal']['member_interface'];
			
			if ($interface_id == '') {
				$interface_id = 6;
			}
		
			$diy_id = $activities[$i]['diy_identifier'];
			
			$info = '<a href="#" onclick="toggle_block_element(\'activity-description-' . $activities[$i]['activity_id'] . '\'); return false;" title="View activity description">' . portal_icon('info') . '</a>';
			
			$run = '<a href="/diy/run/' . $diy_id . '/" title="Run this activity">' . portal_icon('run') . '</a>';
			
			$activity_options = '
			' . $info . '
			' . $run . '
			';
			
			if (in_array($diy_id, $used_activities)) {
				$activity_used = portal_icon('work');
			}
			
		}

		$description = portal_web_output_filter($activities[$i]['activity_description']);
		
		$activity_box = '
		<div class="activity-box">
			<div class="activity-title">
			' . $activity_options . ' ' . portal_web_output_filter($activities[$i]['activity_name']) . ' ' . $activity_used . ' 
			</div>
			<!--div class="activity-info">
			(Sensor: ' . $activities[$i]['sensor_type'] . '; Model: ' . $activities[$i]['model_type'] . ')
			</div-->
			<div class="activity-description" id="activity-description-' . $activities[$i]['activity_id'] . '">
			' . $description . '
			</div>
		</div>
		';
		
		$list .= $activity_box;
	
	}
	
	return $list;

}

function portal_lookup_diy_probe_type($probe_id) {

	static $lookup = array();
	
	if (count($lookup) == 0) {
	
		$query = 'SELECT id, name FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'probe_types';
		$params = array();
		
		$results = mystery_select_query($query, $params, 'rails_dbh');
		
		$lookup = mystery_convert_results_to_lookup_array($results, 'id', 'name');
	
	}
	
	if (@$lookup[$probe_id] == '') {
		$lookup[$probe_id] = 'unknown probe';
	}
	
	return $lookup[$probe_id];

}

function portal_lookup_project_info($project_short_name) {

	static $lookup = array();
	
	if (count($lookup) == 0) {
	
		$query = 'SELECT * FROM portal_projects';
		$params = array();
		
		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$lookup = mystery_convert_results_to_full_lookup_array($results, 'project_name');
	
	}
	
	if (@$lookup[$project_short_name] == '') {
		$lookup[$project_short_name] = array();
	}
	
	return $lookup[$project_short_name];

}

function portal_lookup_diy_model_type($probe_id) {


	/*LEFT JOIN itsidiy_models AS im
	ON ida.model_id=im.id
	LEFT JOIN itsidiy_model_types AS mt
	ON im.model_type_id=mt.id*/

	static $lookup = array();
	
	if (count($lookup) == 0) {
	
		$query = 'SELECT im.id, mt.name FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'models AS im LEFT JOIN ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'model_types AS mt ON im.model_type_id=mt.id';
		$params = array();
		
		$results = mystery_select_query($query, $params, 'rails_dbh');
		
		$lookup = mystery_convert_results_to_lookup_array($results, 'id', 'name');
	
	}
	
	if (@$lookup[$probe_id] == '') {
		$lookup[$probe_id] = 'unknown model';
	}
	
	return $lookup[$probe_id];

}

function portal_lookup_diy_uuid($diy_id) {

	static $lookup = array();
	
	if (count($lookup) == 0) {
	
		$query = 'SELECT id, uuid FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . $GLOBALS['portal_config']['diy_activities_name'];
		$params = array();
		
		$results = mystery_select_query($query, $params, 'rails_dbh');
		
		$lookup = mystery_convert_results_to_lookup_array($results, 'id', 'uuid');
	
	}
	
	if (@$lookup[$diy_id] == '') {
		$lookup[$diy_id] = '';
	}
	
	return $lookup[$diy_id];

}

function portal_get_diy_activities_from_db($conditions = array(), $params = array(), $options = array()) {

	// I guess the right way would be to do this... http://itsidiy.concord.org/users/9/activities.xml

	$query = '
	SELECT 
	ida.id AS activity_id, 
	ida.id AS diy_identifier, 
	ida.name AS activity_name, 
	ida.public,
	ida.description AS activity_description,
	login AS author,
	first_name,
	last_name,
	"DIY" AS level_name,
	"999" AS level_id,
	collectdata_probe_active,
	probe_type_id,
	collectdata_model_active,
	model_id,
	collectdata2_probe_active,
	collectdata2_probetype_id,
	collectdata2_model_active,
	collectdata2_model_id,
	collectdata3_probe_active,
	collectdata3_probetype_id,
	collectdata3_model_active,
	collectdata3_model_id,
	further_model_active,
	further_model_id,
	further_probe_active,
	further_probetype_id,
	"My Activities" AS subject_name,
	CONCAT(last_name, ", ", first_name) AS unit_name
	FROM ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'activities AS ida
	LEFT JOIN ' . $GLOBALS['portal_config']['diy_database'] . '.' . $GLOBALS['portal_config']['diy_table_prefix'] . 'users AS idu
	ON ida.user_id=idu.id
	';

	//"Custom Activities" AS unit_name


	$query_conditions = array();
	$query_params = array();
	
	if (!in_array('no restrict', $options)) {
	
		$query_conditions[] = '(ida.public = ? OR login = ?)';
		$query_params[] = 1;
		$query_params[] = $_SESSION['portal']['member_username'];
	
	}
	
	for ($i = 0; $i < count($conditions); $i++) {
		$query_conditions[] = $conditions[$i];
	}
	
	for ($i = 0; $i < count($params); $i++) {
		$query_params[] = $params[$i];
	}
	
	if (count($query_conditions) > 0) {
		$query .= ' WHERE ' . implode(' AND ', $query_conditions);
	}
	
	$query .= ' ORDER BY ida.name';
	
	$results = mystery_select_query($query, $query_params, 'rails_dbh');

	$results = portal_set_diy_sensor_model_types($results);
	
	return $results;

}

function portal_set_diy_sensor_model_types($results) {

	// this function tries to determine a nice name/display for the models and probes in an ITSI DIY activity
	
	$result_count = count($results);
	
	for ($i = 0; $i < $result_count; $i++) {
	
		// first do probes/sensors
	
		$probes = array();
		
		if ($results[$i]['collectdata_probe_active'] > 0) {
			$probes[] = portal_lookup_diy_probe_type($results[$i]['probe_type_id']);
		}
		
		if ($results[$i]['collectdata2_probe_active'] > 0) {
			$probes[] = portal_lookup_diy_probe_type($results[$i]['collectdata2_probetype_id']);
		}
		
		if ($results[$i]['collectdata3_probe_active'] > 0) {
			$probes[] = portal_lookup_diy_probe_type($results[$i]['collectdata3_probetype_id']);
		}
		
		if ($results[$i]['further_probe_active'] > 0) {
			$probes[] = portal_lookup_diy_probe_type($results[$i]['further_probetype_id']);
		}
		

		if (count($probes) > 0) {
			$results[$i]['sensor_type'] = implode(', ', array_merge(array_unique($probes)));
		} else {
			$results[$i]['sensor_type'] = 'None';
		}
		

		// now do the models

		$models = array();

		if ($results[$i]['collectdata_model_active'] > 0) {
			$models[] = portal_lookup_diy_model_type($results[$i]['model_id']);
		}
		
		if ($results[$i]['collectdata2_model_active'] > 0) {
			$models[] = portal_lookup_diy_model_type($results[$i]['collectdata2_model_id']);
		}
		
		if ($results[$i]['collectdata3_model_active'] > 0) {
			$models[] = portal_lookup_diy_model_type($results[$i]['collectdata3_model_id']);
		}
		
		if ($results[$i]['further_model_active'] > 0) {
			$models[] = portal_lookup_diy_model_type($results[$i]['further_model_id']);
		}
		
		if (count($models) > 0) {
			$results[$i]['model_type'] = implode(', ', array_merge(array_unique($models)));
		} else {
			$results[$i]['model_type'] = 'None';
		}
	
	}
	
	return $results;

}

function portal_record_sort($records, $fields) {

	$hash = array();
	
	foreach($records as $key => $record) {
	
		$hash_key = '';
		
		for ($i = 0; $i < count($fields); $i++) {
			$hash_key .= $record[$fields[$i]];
		}
		
		$hash_key .= $key;
	
		$hash[$hash_key] = $record;

	}
	
	uksort($hash, 'strnatcasecmp');
	
	$records = array();
	
	foreach($hash as $record) {
		$records[] = $record;
	}
	
	return $records;

}

function portal_get_prepared_diy_activities($member_id) {

	if ($GLOBALS['portal_config']['use_diy_activities'] == 'no') {
		return array();
	}

	$member_info = portal_get_member_info($member_id);
	
	// get my students
	
	$query = 'SELECT member_id FROM portal_class_students WHERE class_id IN ("' . implode('","', $member_info['classes']['teacher']) . '")';
	$params = array();
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	$my_students = mystery_convert_results_to_simple_array($results, 'member_id');
	
	
	$query = 'SELECT member_username, member_id, member_school,member_source,school_district AS member_district FROM portal_members AS pm LEFT JOIN portal_schools AS ps ON pm.member_school=ps.school_id LEFT JOIN portal_districts AS pd ON ps.school_district=pd.district_id';
	$params = array();
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	// prepare the member usernames to be an associative_array
		
	$total_members = count($results);
	
	$subject_keys = array();

	for ($i = 0; $i < $total_members; $i++) {
	
		$this_username = $results[$i]['member_username'];
		$this_member_id = $results[$i]['member_id'];
		$this_school = $results[$i]['member_school'];
		$this_district = $results[$i]['member_district'];
		$this_source = $results[$i]['member_source'];
		
		if ($this_username == $member_info['member_username']) {
			$subject_keys[$this_username] = 'My Activities';
		} elseif (in_array($this_member_id, $my_students)) {
			$subject_keys[$this_username] = 'My Student Activities';
		} elseif ($this_school == $_SESSION['portal']['member_school']) {
			$subject_keys[$this_username] = 'My School Activities';
		} elseif ($this_district == $_SESSION['portal']['member_district']) {
			$subject_keys[$this_username] = 'My District Activities';
		} elseif ($this_source == $_SESSION['portal']['member_source'] && trim($this_source) != '') {
			$subject_keys[$this_username] = 'My Event Activities';
		} else {
			$subject_keys[$this_username] = 'Other Activities';
		}
	
	}
	
	$new_activities = array();

	
	// don't show the pre-existing ids
	$conditions = array();
	
	$conditions[] = 'ida.id NOT IN ("' . implode('","', portal_get_diy_ids_to_exclude()) . '")';

	$activities = portal_get_diy_activities_from_db($conditions);
	
	//mystery_debug_query('rails_dbh');
	
	$total_activities = count($activities);
	
	for ($i = 0; $i < $total_activities; $i++) {
	
		if (!isset($subject_keys[$activities[$i]['author']])) {
			$subject_keys[$activities[$i]['author']] = 'Other Activities';
		}
	
		$activities[$i]['subject_name'] = $subject_keys[$activities[$i]['author']];
		
		if (!$activities[$i]['public']) {
			$activities[$i]['activity_name'] .= ' (private)';
		}
	
	}
	
	// now I need to re-sort the array
	
	$activities = portal_record_sort($activities, array('subject_name', 'unit_name', 'activity_name'));
	
	return $activities;

}

function portal_get_diy_ids_to_exclude() {

	global $_PORTAL;

	$query = 'SELECT diy_identifier FROM portal_activities AS pa LEFT JOIN portal_units AS pu ON pa.activity_unit=pu.unit_id WHERE pu.unit_project = ?';
		
	$params = array($_PORTAL['project_info']['project_id']);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	$ids = mystery_convert_results_to_simple_array($results, 'diy_identifier');
	
	return $ids;

}

function portal_get_activities($conditions = array(), $params = array(), $order = 'unit_order, activity_order') {

	global $_PORTAL;

	$query = '
	SELECT * FROM
	portal_activities AS pa
	LEFT JOIN portal_levels AS pl
	ON pa.activity_level=pl.level_id
	LEFT JOIN portal_subjects AS ps
	ON pa.activity_subject=ps.subject_id
	LEFT JOIN portal_units AS pu
	ON pa.activity_unit=pu.unit_id
	LEFT JOIN portal_projects AS pp
	ON pu.unit_project=pp.project_id
	';

	$query_conditions = array();
	$query_params = array();
	
	$query_conditions[] = 'activity_status = ?';
	$query_params[] = 'Ready';
	
	$query_conditions[] = 'project_name = ?';
	$query_params[] = $_PORTAL['project'];

	for ($i = 0; $i < count($conditions); $i++) {
		$query_conditions[] = $conditions[$i];
	}
	
	for ($i = 0; $i < count($params); $i++) {
		$query_params[] = $params[$i];
	}
	
	if (count($query_conditions) > 0) {
		$query .= ' WHERE ' . implode(' AND ', $query_conditions);
	}
	
	if ($order != '') {
	
		$query .= ' ORDER BY ' . $order;
	
	}
	
	$results = mystery_select_query($query, $query_params, 'portal_dbh');
	
	return $results;

}

function portal_get_all_activities($order = 'unit_order, activity_order') {

	$portal_activities = portal_get_activities(array(), array(), $order);
	
	$diy_activities = portal_get_prepared_diy_activities(@$_SESSION['portal']['member_id']);

	$activities = array_merge($portal_activities, $diy_activities);

	return $activities;

}


function portal_get_accommodations($project_id) {

	$query = 'SELECT * FROM portal_accommodations WHERE accommodation_project = ?';
	$params = array($project_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	return $results;
	
}

function portal_get_member_accommodations($member_id) {

	$query = 'SELECT * FROM  portal_accommodation_usage WHERE usage_type = ? AND usage_type_id = ?';
	$params = array('member', $member_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	return $results;
	
}

function portal_get_class_accommodations($class_id) {

	$query = 'SELECT * FROM portal_accommodation_usage WHERE usage_type = ? AND usage_type_id = ?';
	$params = array('class', $class_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	return $results;
	
}

function portal_generate_member_accommodations($member_id) {

	$list = '';

	if (@$GLOBALS['portal_config']['use_accommodations'] == 'yes') {
	
	
	}

	return $list;

}


function portal_generate_class_accommodations($class_id) {

	$list = '';

	if (@$GLOBALS['portal_config']['use_accommodations'] == 'yes') {
	
		$accommodations = portal_get_accommodations($GLOBALS['_PORTAL']['project_info']['project_id']);
				
		$class_settings = portal_get_class_accommodations($class_id);
		
		$list_parts = array();
		
		for ($i = 0; $i < count($accommodations); $i++) {

			$list_parts[] = '<li>' . portal_generate_accommodation_row($accommodations[$i], $class_settings) . '</li>';
		
		}
		
		$list = '<ul>' . implode("\n", $list_parts) . '</ul>';
	
	}

	return $list;

}

function portal_generate_accommodation_row($accommodation, $settings = array()) {

	// this function takes a particular accomodation and displays it in a checkbox row.
	
	//mystery_print_r($accommodation);
	
	return '<strong>' . $accommodation['accommodation_name'] . '</strong>: ' . $accommodation['accommodation_values'];

}

function portal_generate_activity_grid($activity_ids = array(), $diy_activity_ids = array(), $mode = '') {

	global $portal_config;

	$activity_grid = '';
	
	if ($mode == 'preview') {
	
		$activities = portal_get_activities(array(), array(), 'unit_order, activity_order');
	
	} else {

		$activities = portal_get_all_activities();
	
	}
	
	// First create the display activities array
	
	$display_activities = array();
	
	$level_classes = array();
	
	$level_counts = array();
	
	for ($i = 0; $i < count($activities); $i++) {

		$this_level = $activities[$i]['level_name'] . ': ' . $activities[$i]['subject_name'];
		
		$fixed_level = preg_replace('~[^a-z0-9]~','',strtolower($this_level));
		
		$class = 'level' . $activities[$i]['level_id'];
		
		$level_classes[$this_level] = $class;
		
		if (!isset($level_counts[$this_level])) {
			$level_counts[$this_level] = 0;
		}
		
		$this_unit = $activities[$i]['unit_name'];


		// Setup Checkboxes

		$checked = '';
		
		if ($activities[$i]['level_id'] == '999') {
		
			if (in_array($activities[$i]['activity_id'], $diy_activity_ids)) {
				$checked = ' checked="checked"';
				$level_counts[$this_level]++;
			}
			
			$field_name = 'diy_activities[]';
		
		} else {

			if (in_array($activities[$i]['activity_id'], $activity_ids)) {
				$checked = ' checked="checked"';
				$level_counts[$this_level]++;
			}
			
			$field_name = 'activities[]';
		
		}
				
		$checkbox = '<input type="checkbox" name="' . $field_name . '" value="' . $activities[$i]['activity_id'] . '"' . $checked . ' onclick="updateTotalActivities(this); updateSectionActivities(\'' . $fixed_level . '\', this);">';
	
		// Setup DIY Items
		
		$diy_id = $activities[$i]['diy_identifier'];
		
		$id_prefix = '';
		
		if ($activities[$i]['level_id'] == '999') {
			$id_prefix = 'diy';
		}
		
		// initialize placeholders for links
		
		$copy = '';
		$edit = '';
		$info = '';
		$preview = '';
		$report = '';
		$run = '';
		$try = '';

		if ($diy_id != '') {
		
			if (in_array('copy', $GLOBALS['portal_config']['available_actions'])) {
				$copy_title = 'Make your own version of this activity';
				$copy = '<a href="/diy/copy/' . $diy_id . '/" target="_blank" title="' . $copy_title . '">' . portal_icon('copy', $copy_title) . '</a>';
			}
			
			if (in_array('edit', $GLOBALS['portal_config']['available_actions'])) {
				$edit_title = 'Edit this activity';
				$edit = '<a href="/diy/edit/' . $diy_id . '/" target="_blank" title="' . $edit_title . '">' . portal_icon('setup', $edit_title) . '</a>';
			}
			
			if (in_array('info', $GLOBALS['portal_config']['available_actions'])) {
				$info_title = 'View activity description';
				$info = '<a href="#" onclick="toggle_block_element(\'activity-description-' . $id_prefix . $activities[$i]['activity_id'] . '\'); return false;" title="' . $info_title . '">' . portal_icon('info', $info_title) . '</a>';
			}
			
			if (in_array('preview', $GLOBALS['portal_config']['available_actions'])) {
				$preview_title = 'View a quick preview version of this activity';
				$preview = '<a href="/diy/show/' . $diy_id . '/" target="_blank" title="' . $preview_title . '">' . portal_icon('preview', $preview_title) . '</a>';
			}
			
			if (in_array('report', $GLOBALS['portal_config']['available_actions'])) {
				$report_title = 'View the student data from this activity';
				$report = '<a href="/diy/usage/' . $diy_id . '/" target="_blank" title="' . $report_title . '">' . portal_icon('report', $report_title) . '</a>';
			}
			
			if (in_array('run', $GLOBALS['portal_config']['available_actions'])) {
				$run_title = 'Run this activity (and save data)';
				$run = '<a href="/diy/run/' . $diy_id .  '/" title="' . $run_title . '">' . portal_icon('run', $run_title) . '</a>';
			}
			
			if (in_array('try', $GLOBALS['portal_config']['available_actions'])) {
				$try_title = 'Try this activity (as a teacher, do not save data)';
				$try = '<a href="/diy/view/' . $diy_id .  '/" title="' . $try_title . '">' . portal_icon('try', $try_title) . '</a>';
			}
			
		}
		
		// don't show an edit link if hte user can't edit this activity
		
		if ($activities[$i]['subject_name'] != 'My Activities') {
			$edit = '';
		}
		
		// remove items based on the mode
		
		if ($mode == 'setup') {
			$copy = '';
			$edit = '';
			$run = '';
			$report = '';
			$try = '';
			$preview = '';
		} else {
			$checkbox = '';
		}
		
		if ($mode == 'preview') {
			$report = '';
			$run = '';
			$copy = '';
		}

		$activity_options = '
		' . $checkbox . '
		' . $edit . '
		' . $copy . '
		' . $info . '
		' . $report . '
		' . $preview . '
		' . $try . '
		' . $run . '
		';
		
		$description = portal_web_output_filter($activities[$i]['activity_description']);
		
		$sensor_probe_string = '';
		
		if (@$activities[$i]['sensor_type'] != 'None' || @$activities[$i]['model_type'] != 'None') {
			
			$sensor_probe_string_parts = array();
			
			if (@$activities[$i]['sensor_type'] != 'None') {
				$sensor_probe_string_parts[] = 'Sensor: ' . @$activities[$i]['sensor_type'];
			}
		
			if (@$activities[$i]['model_type'] != 'None') {
				$sensor_probe_string_parts[] = 'Model: ' . @$activities[$i]['model_type'];
			}
			
			$sensor_probe_string = '(' . implode('; ', $sensor_probe_string_parts) . ')';
			
		}
		
		$activity_box = '
		<div class="activity-box">
			<div class="activity-title">
			' . $activity_options . ' ' . portal_web_output_filter($activities[$i]['activity_name']) . ' 
			</div>
			<div class="activity-info">
			' . $sensor_probe_string . '
			</div>
			<div class="activity-description" id="activity-description-' . $id_prefix . $activities[$i]['activity_id'] . '">
			' . $description . '
			</div>
		</div>
		';
		
		$display_activities[$this_level][$this_unit][] = $activity_box;
	
	}
	
	// Now loop through the display activities array and generate the unit displays
	
	$navigation = array();
	$panels = array();
	
	$i = 0;
	
	$js_section = '';
	$js_section_control = '';
	
	while (list($level, $unit_set) = each($display_activities)) {
	
		$fixed_level = preg_replace('~[^a-z0-9]~','',strtolower($level));
		
		$level_count = $level_counts[$level];
		
		$fixed_level_count = '';
		
		if ($level_count > 0) {
			$fixed_level_count = ' (' . $level_count . ')';
		}

		$navigation[] = '<li class="unit-navigation ' . $level_classes[$level] . '" id="' . $fixed_level . '-control" onclick="show_section(\'' . $fixed_level . '\', this);">' . $level . ' <span id="' . $fixed_level .'-count" class="navigation-count">' . $fixed_level_count . '</span></li>';
		
		if ($i == 0) {
			$js_section = $fixed_level;
			$js_section_control = $fixed_level . '-control';
		}
		
		$this_panel = '
		<div class="unit-activities ' . $level_classes[$level] . '" id="' . preg_replace('~[^a-z0-9]~','',strtolower($level)) . '">
		<script type="text/javascript">
			if (window.sectionActivities === undefined) {
				sectionActivities = [];
			}
			sectionActivities["' . $fixed_level . '"] = ' . $level_count . ';
		</script>
		';
		
		reset($unit_set);
		
		while (list($unit_title, $activity_boxes) = each($unit_set)) {
		
			$unit_id = preg_replace('~[^a-z0-9]~','',strtolower($level . $unit_title));
		
			$select_all = '';
			
			if ($mode == 'setup') {
				$select_all = '<span class="heading-info"><label><input type="checkbox" onclick="select_activity_checkboxes(\'' . $unit_id . '\', \'' . $fixed_level . '\', this);"> select all</label></span>';
			}
		
			$this_panel .= '
			<div id="' . $unit_id . '">
			<h2 class="unit-title">' . portal_web_output_filter($unit_title) . ' ' . $select_all . '</h2>
			
			' . implode("\n", $activity_boxes) . '
			</div>
			';
		
		}
		
		$this_panel .= '
		</div>
		';
		
		$panels[] = $this_panel;
		
		$i++;
	
	}
	
	// Now generate the interface
	
	$activity_grid = '
	' . portal_generate_icon_legend() . '
	<table id="activity-chart" border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td id="activity-chart-navigation">
				<ul>' . implode("\n", $navigation) . '</ul>
			</td>
			<td id="activity-chart-panels">
			' . implode("\n", $panels) . '
			</td>
		</tr>
	</table>
	
	<script type="text/javascript">
		show_section("' . $js_section . '", document.getElementById("' . $js_section_control . '"));
		
	</script>
	';

	return $activity_grid;
	
}


?>
