<?php

$member_id = $_PORTAL['action'];

$member_info = portal_get_member_info($member_id);

$is_editing_self = 'no';
$page_title = 'Member Edit';
$selected_member_is_members_student = 'no';


$return_page = '/admin/';

if ($member_id == $_SESSION['portal']['member_id']) {

	$is_editing_self = 'yes';
	$page_title = 'My Information';
	$return_page = '/';
	
	if ($_SESSION['portal']['member_type'] == 'student') {
		$return_page = '/member/info/';
	}

} elseif ($_SESSION['portal']['member_type'] == 'teacher') {

	// check to see if the selected member is in the member's class

	$taught_classes = portal_get_teacher_classes($_SESSION['portal']['member_id']);
	
	if (array_intersect($taught_classes, $member_info['classes']['student'])) {
	
		$selected_member_is_members_student = 'yes';
		$return_page = '/';
		
	}

}

if ($_SESSION['portal']['member_type'] != 'superuser' && $_SESSION['portal']['member_type'] != 'admin' && $is_editing_self != 'yes' && $selected_member_is_members_student != 'yes') {

	mystery_redirect('/');
	exit;

}

if (isset($_PORTAL['params']['process'])) {

	$data = array();
	
	$errors = array();

	if ($_REQUEST['password'] != '') {

		$data['member_password'] = md5(strtolower($_REQUEST['password']));
		$data['member_password_ue'] = $_REQUEST['password'];

		if (strlen($_REQUEST['password']) < 4 || strlen($_REQUEST['password']) > 40) {
			$errors[] = 'Your password must be between 4 and 40 characters long.';
		}

	}
	
	if (isset($_REQUEST['email']) && $_REQUEST['email'] != '') {
		$data['member_email'] = $_REQUEST['email'];
	} else {
		$_REQUEST['email'] = $member_info['member_email'];
	}

	$data['member_first_name'] = $_REQUEST['first_name'];
	$data['member_last_name'] = $_REQUEST['last_name'];
	$data['member_interface'] = $_REQUEST['interface'];


	// FIXME - add the admin role if appropriate/requested
	// $data['member_type'] = $request['type'];

	if (count($errors) == 0) {
	
		$status = mystery_update_query('portal_members', $data, 'member_id', $member_id, 'portal_dbh');
		
		if ($status == 0) {
			$errors[] = 'Could not update member information';
		}
		
		portal_update_cc_member_info($member_info['cc_member_id'], $_SESSION['portal']['member_username'], $_REQUEST['password'], $_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['email']);
		
		portal_update_diy_member_info(portal_get_diy_member_id_from_db($member_info['member_username']), $_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['email'], $_REQUEST['interface']);
	
	}
	
	if (count($errors) > 0) {
	
		echo portal_generate_error_page($errors);
	
	} else {
	
		if ($is_editing_self == 'yes') {
		
			// fix up the session information
			$_SESSION['user_first_name'] = $_REQUEST['first_name'];
			$_SESSION['user_last_name'] = $_REQUEST['last_name'];
			$_SESSION['portal']['member_interface'] = $_REQUEST['interface'];

			if (isset($_REQUEST['email'])) {
				$_SESSION['user_email'] = $_REQUEST['email'];
			}
			
			if ($_REQUEST['password'] != '') {
				$_SESSION['portal']['member_password_ue'] = $_REQUEST['password'];
			}
		
		}
		
		// update class information if provided
		
		if (isset($_REQUEST['class_id'])) {
		
			if (!in_array($_REQUEST['class_id'], $member_info['classes']['student'])) {
				portal_add_member_to_class($member_id, $_REQUEST['class_id']);
			}

		}
	
		// redirect back to the admin page
		
		mystery_redirect($return_page);
		exit;
	
	}
	
} else {

	// FIXME - show admin option for admin/superuser
	
	$member_info = portal_web_output_filter($member_info);

	$email_field = '';
	$class_field = '';
		
	if ($member_info['member_type'] == 'student') {

		if ($_SESSION['portal']['member_type'] != 'student') {

			// don't let students change their own class
			$teacher_id = '';

			if ($_SESSION['portal']['member_type'] == 'teacher') {
				$teacher_id = $_SESSION['portal']['member_id'];
			}

			$current_class = @$member_info['classes']['student'][0];

			$class_field = portal_generate_class_select_list(@$member_info['member_school'], $teacher_id, $current_class);
			
		}

	} else {

		$email_field = '<p><label for="email">Email</label> <input type="text" name="email" id="email" value="' . @$member_info['member_email'] . '" size="35"></p>';

	}

	echo '
	<form action="/member/edit/' . $member_id . '/process/" method="post">
		
	<p><label for="username">Username</label> <strong>' . @$member_info['member_username'] . '</strong></p>

	<p><label for="first-name">First Name</label> <input type="text" name="first_name" id="first-name" value="' . @$member_info['member_first_name'] . '" size="35"></p>

	<p><label for="last-name">Last Name</label> <input type="text" name="last_name" id="last-name" value="' . @$member_info['member_last_name'] . '" size="35"></p>
	
	' . $email_field . '

	<p><label for="password">New Password</label> <input type="text" name="password" id="password" value="" size="35"> <span class="form-field-info"><strong>Warning:</strong> this field will display your password<br><strong>Note:</strong> your password must be between 4 and 40 characters long</span></p>

	<p><label for="interface">Interface</label> ' . portal_generate_interface_list(@$member_info['member_interface']) . '</p>

	' . $class_field . '

	<p><label for="submit">&nbsp;</label> <input type="submit" id="submit" value="Save"></p>
	
	<div class="clear-both">&nbsp;</div>
	
	</form>
	';

}

?>
