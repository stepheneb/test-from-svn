<?php

$page_title = 'Class Roster';

$class_id = $_PORTAL['action'];

$class_info = portal_get_class_info($class_id);

if (count($class_info) == 0) {

	$errors = array();
	
	$errors[] = 'The specified class does not exist.';
	
	echo portal_generate_error_page($errors);
	
	return;

}

if ($_SESSION['portal']['member_type'] != 'superuser' && $_SESSION['portal']['member_type'] != 'admin' && ($_SESSION['portal']['member_type'] != 'teacher' || $class_info['class_teacher']  != $_SESSION['portal']['member_id'])) {

	mystery_redirect('/');
	exit;

}

// get a list of all the students in this class and display their information

$students = portal_get_class_students($class_id);

$student_count = count($students);

echo '
<h2>Class Roster: ' . $class_info['class_name'] . '</h2>

<p>' . $student_count . ' students enrolled in this class</p>

<table class="roster-table">

<tr>
<th>&nbsp;</th>
<th>First Name</th>
<th>Last Name</th>
<th>Username</th>
<th>Password</th>
<th>Interface</th>
</tr>

';

for ($i = 0; $i < $student_count; $i++) {

	$edit_link = '<a href="/member/edit/' . $students[$i]['member_id'] . '/">' . portal_icon('setup') . '</a>';

	echo '
	<tr>
	<td>' . $edit_link . '</td>
	<td>' . $students[$i]['member_first_name'] . '</td>
	<td>' . $students[$i]['member_last_name'] . '</td>
	<td>' . $students[$i]['member_username'] . '</td>
	<td>' . $students[$i]['member_password_ue'] . '</td>
	<td>' . $portal_config['interfaces'][$students[$i]['member_interface']] . '</td>
	</tr>
	';


}

echo '
</table>
';

?>
