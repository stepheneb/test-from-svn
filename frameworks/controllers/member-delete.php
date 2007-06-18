<?php

$member_id = $_PORTAL['action'];

$member_info = portal_get_member_info($member_id);

$is_editing_self = 'no';
$page_title = 'Member Delete';
$selected_member_is_members_student = 'no';

$return_page = '/admin/';

if ($_SESSION['portal']['member_type'] == 'teacher') {

	$return_page = '/';
		
	// check to see if the selected member is in the member's class

	$taught_classes = portal_get_teacher_classes($_SESSION['portal']['member_id']);
	
	if (array_intersect($taught_classes, $member_info['classes']['student'])) {
	
		$selected_member_is_members_student = 'yes';
	}

}

if ($_SESSION['portal']['member_type'] != 'superuser' && $_SESSION['portal']['member_type'] != 'admin' && $selected_member_is_members_student != 'yes') {

	mystery_redirect('/');
	exit;

}


if (isset($_PORTAL['params']['process'])) {

	if ($member_id == $_SESSION['portal']['member_id']) {
	
		$errors = array('Sorry you cannot delete your own account.');
		
		echo portal_generate_error_page($errors);
	
	} else {

		portal_delete_member($_PORTAL['action']);
		mystery_redirect($return_page);
		exit;

	}
	
} else {

	// FIXME - Maybe add in details on the class to be deleted here

	echo '
	<form action="/member/delete/' . $member_id . '/process/" method="post">
	<h1>Delete a member?</h1>
	
	<p>Are you <strong>absolutely sure</strong> that you want to delete the member <strong>' . $member_info['member_first_name'] . ' ' . $member_info['member_last_name'] . '</strong>?  There is no undo available.</p>
	
	<p><input type="button" value="No" onclick="history.back();"> <input type="submit" value="Yes"></p>
	</form>
	';

}


?>
