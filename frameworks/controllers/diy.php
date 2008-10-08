<?php

// This controller handles most interactions with the DIY

$diy_action = $_PORTAL['activity'];

$diy_id = $_PORTAL['action'];

// prefer to use a uuid if one is specified and the project settings desire it

if ($portal_config['diy_use_uuid'] =='yes') {
	
	$uuid = portal_lookup_diy_uuid($diy_id);
	
	if ($uuid != '') {
		$diy_id = $uuid;
	}

}

$student_id = @$_PORTAL['params']['student'];

// These actions require logging into the DIY first, so we'll do that

if (@$_SESSION['is_logged_in'] == 'yes') {

	portal_setup_diy_session();
	
	//$member_id = @$_SESSION['diy_member_id'];
	
	//if (!$member_id) {
	
		$member_id = portal_get_diy_member_id($_SESSION['portal']['member_id']);
		
		//trigger_error('SESSION: ' . $_SESSION['portal']['member_id'] . '; Lookup: ' . $member_id);
		
		//trigger_error($_MYSTERY['rails_dbh']->last_query);
		
		if ($member_id == '') {
			$member_id = 1;
		}

	//}

} else {

	// Use the anonymous member
	$member_id = 1;

}

// some of the actions require an interface, so we'll set it up just in case

$interface_id = @$_SESSION['portal']['member_interface'];

if ($interface_id == '') {
	$interface_id = 6;
}

switch($portal_config['diy_param_order']) {

	case 'member/interface':
		$member_interface_path = $member_id . '/' . $interface_id;
		$student_interface_path = $student_id . '/' . $interface_id;
	break;
	
	case 'interface/member':
		$member_interface_path = $interface_id . '/' . $member_id;
		$student_interface_path = $interface_id . '/' . $student_id;
	break;

	default:
		$member_interface_path = '';
		$student_interface_path = '';
	break;

}

$reporting_param = $portal_config['diy_reporting_parameter'];
$class_identifier = portal_get_class_identifier($_SESSION['portal']['member_id']);

switch($diy_action) {

	case 'new':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/new/';
	
	break;
	
	case 'home':
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/';
	break;
	
	case 'copy':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/copy';
	
	break;
	
	case 'edit':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/edit';
	
	break;
	
	case 'view':

		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/sail_jnlp/' . $member_interface_path . '/preview' . '?group_id=' . $class_identifier;
	
	break;
	
	case 'report':
	
		$teacher_name = urlencode(base64_decode(rawurldecode($_PORTAL['params']['teacher'])));
		$class_name = urlencode(base64_decode(rawurldecode($_PORTAL['params']['class'])));
		$member_list = urlencode(base64_decode(rawurldecode($_PORTAL['params']['members'])));
    $class_identifier = urlencode(base64_decode(rawurldecode($_PORTAL['params']['uuid'])));
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/reports/' . $diy_id . '/sail_jnlp?users=' . $member_list . '&system.report.class.name=' . $class_name . '&system.report.teacher.name=' . $teacher_name . '&group_id=' . $class_identifier;
	
	break;
	
	case 'run':

		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/sail_jnlp/' . $member_interface_path . '?group_id=' . $class_identifier;
	
	break;

	case 'show':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id;

	break;
	
	case 'usage':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/usage';

	break;
	
	case 'work':
  
	  $class_identifier = portal_get_class_identifier($student_id);
	  $class_id_prefix =  strlen($reporting_param) == 0 ? '?group_id=' : '&group_id=';
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/sail_jnlp/' . $student_interface_path . '/view' . $reporting_param . $class_id_prefix . $class_identifier;
	
	break;
	
	
}

mystery_redirect($url);
exit;

?>
