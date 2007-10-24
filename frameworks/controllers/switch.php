<?php

$page_title = 'Switch';

if ($_SESSION['portal']['member_type'] != 'superuser') {

	mystery_redirect('/');
	exit;

}

if ($_PORTAL['activity'] == 'school' && $_PORTAL['action'] == 'process' && $_REQUEST['school_id'] != $_SESSION['portal']['member_school']) {
	$_SESSION['portal']['member_school'] = $_REQUEST['school_id'];
	$member_info = portal_get_member_info($_SESSION['portal']['member_id']);
}

if ($_PORTAL['activity'] == 'member' && $_PORTAL['action'] == 'process' && $_REQUEST['school_id'] != $_SESSION['portal']['member_school']) {
	$member_info = portal_get_member_info($_REQUEST['member_id']);
	$_SESSION['portal']['member_id'] = $_REQUEST['member_id'];
	$_SESSION['user_first_name'] = $member_info['member_first_name'];
	$_SESSION['user_last_name'] = $member_info['member_last_name'];
	$_SESSION['user_username'] = $member_info['member_username'];
	$_SESSION['user_email'] = $member_info['member_email'];
	$_SESSION['portal']['member_id'] = $member_info['member_id'];
	$_SESSION['portal']['member_school'] = $member_info['member_school'];
	$_SESSION['portal']['member_interface'] = $member_info['member_interface'];
	$_SESSION['portal']['diy_member_id'] = $member_info['diy_member_id'];
	$_SESSION['portal']['sds_member_id'] = $member_info['sds_member_id'];
	$_SESSION['portal']['member_username'] = $member_info['member_username'];
	$_SESSION['portal']['member_password_ue'] = $member_info['member_password_ue'];
	$_SESSION['portal']['taking_course'] = $member_info['taking_course'];
}

$school_info = portal_get_school_info($_SESSION['portal']['member_school']);


echo '
<h1>Switch your school</h1>

<p>You are currently in the school:</p>

<p><strong>' . $school_info['school_name'] . '</strong><br>
' . $school_info['school_city'] . ', ' . $school_info['school_state'] . ' ' . $school_info['school_zip'] . '
</p>

<form action="/switch/school/process/" method="post">

<p><label for="school-id">Switch to</label> <select name="school_id" id="school-id"><option value="">Please select…</option>' . portal_generate_school_option_list() . '</select> <input type="submit" value="Go"></p>

</form>


<h1>Switch your member</h1>

<p>You are currently the member:</p>

<p><strong>' . $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name'] . '</strong><br>
Email: ' . $_SESSION['user_email'] . '<br>
User: ' . $_SESSION['user_username'] . '
</p>

<form action="/switch/member/process/" method="post">

<p><label for="member-id">Switch to</label> <select name="member_id" id="member-id"><option value="">Please select…</option>' . portal_generate_member_option_list() . '</select> <input type="submit" value="Go"></p>

</form>

<p><strong>Note:</strong> If you are trying to switch to be a student, you\'ll need to visit the <a href="/studenthome/">student home page using this link</a>.</p>
';

?>