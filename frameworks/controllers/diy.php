<?php

// This controller handles most interactions with the DIY

$diy_action = $_PORTAL['activity'];

$diy_id = $_PORTAL['action'];

// prefer to use a uuid if one is specified

$uuid = portal_lookup_diy_uuid($diy_id);

if ($uuid != '') {
	$diy_id = $uuid;
}

$student_id = @$_PORTAL['params']['student'];

// These actions require logging into the DIY first, so we'll do that

if (@$_SESSION['is_logged_in'] == 'yes') {

	portal_setup_diy_session();
	
	$member_id = @$_SESSION['diy_member_id'];
	
	if (!$member_id) {
	
		$member_id = portal_get_diy_member_id($_SESSION['portal']['member_id']);

	}

} else {

	// Use the anonymous member
	$member_id = 1;

}

// some of the actions require an interface, so we'll set it up just in case

$interface_id = @$_SESSION['portal']['member_interface'];

if ($interface_id == '') {
	$interface_id = 6;
}


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

		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/sail_jnlp/' . $interface_id . '/' . $member_id . '/view';
	
	break;
	
	case 'run':

		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/sail_jnlp/' . $interface_id . '/' . $member_id;
	
	break;

	case 'show':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id;

	break;
	
	case 'usage':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/usage';

	break;
	
	case 'work':
	
		$url = 'http://' . $portal_config['diy_server'] . $portal_config['diy_server_path'] . '/' . $portal_config['diy_activities_name'] . '/' . $diy_id . '/sail_jnlp/' . $interface_id . '/' . $student_id . '/view';
	
	break;
	
	
}

mystery_redirect($url);
exit;

?>