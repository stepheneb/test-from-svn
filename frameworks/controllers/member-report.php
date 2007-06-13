<?php

$page_title = 'Student Report';

$student_id = $_PORTAL['action'];

if ($_SESSION['portal']['member_type'] == 'student' && $_SESSION['portal']['member_id'] != $student_id) {

	// let this individual student but no other see this work.

	mystery_redirect('/');
	exit;

}

$member_info = portal_get_member_info($student_id);

$class_info = portal_get_class_info($member_info['classes']['student'][0]);


echo '
<h2>Report for ' . $member_info['member_first_name'] . ' ' . $member_info['member_last_name'] . ' in ' . $class_info['class_name'] . '</h2>

<p>We apologize but this feature is not yet available.  It will look something like the image below:</p>

<p><img src="/images/screenshots/student-report.png"></p>

';

?>