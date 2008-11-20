<?php

$page_title = 'Class Report';

if ($_SESSION['portal']['member_type'] == 'student') {

	mystery_redirect('/');
	exit;

}

$class_id = $_PORTAL['action'];

$class_info = portal_get_class_info($class_id);

echo '
<h2>Report for ' . $class_info['class_name'] . '</h2>
';

$old_uuid_setting = $GLOBALS['portal_config']['diy_use_uuid'];
$GLOBALS['portal_config']['diy_use_uuid'] = 'no';
$class_activities = portal_get_class_diy_activities($class_id);
$GLOBALS['portal_config']['diy_use_uuid'] = $old_uuid_setting;

$students = portal_get_class_students($class_id);

echo '
<table class="roster-table">
<tr>
	<th>Student</th>
';

for ($i = 0; $i < count($class_activities); $i++) {

	echo '
	<th>
	<a title="' . $class_activities[$i]['activity_name'] . '">' . $class_activities[$i]['activity_name'] . '</a>
	' . portal_generate_class_aggregate_report_link($class_activities[$i], $class_id) . '
	</th>
	';

}
	
echo '
</tr>
';

for ($h = 0; $h < count($students); $h++) {

	$student_diy_id = portal_get_diy_member_id_from_db($students[$h]['member_username']);
	
	echo '
	<tr>
		<td><a href="/member/report/' . $students[$h]['member_id'] . '">' . $students[$h]['member_last_name'] . ', ' . $students[$h]['member_first_name'] . ' (' . $students[$h]['member_username'] . ')</a></td>
	';
	
	
	$usage = portal_get_diy_activity_usage_from_db($students[$h]['member_id']);

	for ($i = 0; $i < count($class_activities); $i++) {
	
		$report_link = '&nbsp;';
	
		if (in_array($class_activities[$i]['activity_id'], $usage)) {
			$report_link = portal_simple_icon_link('work', '/diy/work/' . $class_activities[$i]['activity_id'] . '/student/' . $student_diy_id . '/', 'View work by ' . $students[$h]['member_first_name'] . ' ' . $students[$h]['member_last_name'] . ' on ' . $class_activities[$i]['activity_name'] . '');
		}
	
		echo '
		<td style="text-align: center;">' . $report_link . '</td>
		';
	
	}
	
	echo '
	</tr>
	';

}

echo '
</table>
';


/*
<p>We apologize but this feature is not yet available.  It will look something like the image below:</p>

<p><img src="/images/screenshots/class-report.png"></p>

<p>For now, please click on <strong>Activities</strong> to the left, find the activity, click the ' . portal_icon('report') . ' icon, then find the student in the list
that is displayed.</p>
*/

?>
