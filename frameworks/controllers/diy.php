<?php

// This controller handles most interactions with the DIY

$diy_action = $_PORTAL['activity'];

$diy_id = $_PORTAL['action'];

// These actions require logging into the DIY first, so we'll do that

portal_setup_diy_session();

switch($diy_action) {

	case 'new':
	
		$url = 'http://' . $portal_config['diy_server'] . '/activities/new/';
	
	break;
	
	case 'home':
		$url = 'http://' . $portal_config['diy_server'] . '/';
	break;
	
	case 'copy':
	
		$url = 'http://' . $portal_config['diy_server'] . '/activities/' . $diy_id . '/copy';
	
	break;
	
	case 'edit':
	
		$url = 'http://' . $portal_config['diy_server'] . '/activities/' . $diy_id . '/edit';
	
	break;
	
	case 'view':

		$interface_id = @$_SESSION['portal']['member_interface'];
		
		if ($interface_id == '') {
			$interface_id = 6;
		}

		$member_id = portal_get_diy_member_id_from_db($_SESSION['portal']['member_username']);

		$url = 'http://' . $portal_config['diy_server'] . '/activities/' . $diy_id . '/sail_jnlp/' . $interface_id . '/' . $member_id . '/view';
	
	break;
	
	case 'run':

		$interface_id = @$_SESSION['portal']['member_interface'];
		
		if ($interface_id == '') {
			$interface_id = 6;
		}
		
		$member_id = portal_get_diy_member_id_from_db($_SESSION['portal']['member_username']);

		$url = 'http://' . $portal_config['diy_server'] . '/activities/' . $diy_id . '/sail_jnlp/' . $interface_id . '/' . $member_id;
	
	break;

	case 'show':
	
		$url = 'http://' . $portal_config['diy_server'] . '/activities/' . $diy_id;

	break;
	
	case 'usage':
	
		$url = 'http://' . $portal_config['diy_server'] . '/activities/' . $diy_id . '/usage';

	break;
}

mystery_redirect($url);
exit;

?>