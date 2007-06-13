<?php

$page_title = 'Home';

$school_id = $_SESSION['portal']['member_school'];

$classes = portal_get_student_classes($_SESSION['portal']['member_id']);

$class_id = $classes[0];

$class_info = portal_get_class_info($class_id);

$page_title = $class_info['class_name'];

echo portal_generate_student_activity_list($_SESSION['portal']['member_id'], $class_id);

echo '<h2 style="margin-top: 1.5em;">Additional Activities</h2>';

$teacher_info = portal_get_member_info($class_info['class_teacher']);

$conditions = array();
$params = array();

//$conditions[] = 'login = ?';
//$params[] = $teacher_info['member_username'];

$conditions[] = 'ida.id IN ("' . implode('","', $class_info['diy_activities']) . '")';

$activities = portal_get_diy_activities_from_db($conditions, $params);

$list = '';

for ($i = 0; $i < count($activities); $i++) {

	$activity_options = '';

	if ($activities[$i]['diy_identifier'] != '') {
	
		$diy_id = $activities[$i]['diy_identifier'];
		
		$info = '<a href="#" onclick="toggle_block_element(\'activity-description-' . $activities[$i]['activity_id'] . '\'); return false;" title="View activity description">' . portal_icon('info') . '</a>';
		
		$run = '<a href="/diy/run/' . $diy_id . '/" title="Run this activity">' . portal_icon('run') . '</a>';
		
		$activity_options = '
		' . $info . '
		' . $run . '
		';
		
	}

	$description = portal_web_output_filter($activities[$i]['activity_description']);
	
	$activity_box = '
	<div class="activity-box">
		<div class="activity-title">
		' . $activity_options . ' ' . portal_web_output_filter($activities[$i]['activity_name']) . ' 
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

echo $list;

//mystery_print_r(portal_get_student_classes($_SESSION['portal']['member_id']));

?>
