<?php

$page_title = 'Student Report';

$student_id = $_PORTAL['action'];

if ($_SESSION['portal']['member_type'] == 'student' && $_SESSION['portal']['member_id'] != $student_id) {

	// let this individual student but no other see this work.

	mystery_redirect('/');
	exit;

}

$member_info = portal_get_member_info($student_id);

$class_id = @$member_info['classes']['student'][0];

$class_info = portal_get_class_info($class_id);

echo '
<h2>Report for ' . $member_info['member_first_name'] . ' ' . $member_info['member_last_name'] . ' in ' . @$class_info['class_name'] . '</h2>
';

$class_activities = portal_get_class_diy_activities($class_id);

$usage = portal_get_diy_activity_usage_from_db($student_id);

echo '
<table class="roster-table">
<tr>
	<th>Activity</th>
	<th>Status</th>
</tr>
';

for ($i = 0; $i < count($class_activities); $i++) {

	$report_link = '&nbsp;';

	if (in_array($class_activities[$i]['activity_id'], $usage)) {
		$report_link = portal_simple_icon_link('work', '/diy/work/' . $class_activities[$i]['activity_id'] . '/student/' . $member_info['diy_member_id'] . '/', 'View this student\'s work');
	}

	echo '
	<tr>
		<td>' . $class_activities[$i]['activity_name'] . '</td>
		<td>' . $report_link . '</td>
	</tr>
	';

}

echo '
</table>
';

/*
<p>We apologize but this feature is not yet available.  It will look something like the image below:</p>

<p><img src="/images/screenshots/student-report.png"></p>

<p>For now, please click on <strong>Activities</strong> to the left, find the activity, click the ' . portal_icon('report') . ' icon, then find the student in the list
that is displayed.</p>
*/


?>