<?php

$page_title = 'Student Sign-up';

if ($_PORTAL['action'] == 'process') {

	$member_id = portal_process_student_registration($_REQUEST);
	
	if ($member_id == 0 || $member_id == '') {
	
		echo portal_generate_error_page($_PORTAL['errors']);
	
	} else {
	
		$next_steps = '';

		if ($_PORTAL['section'] == 'signup') {
		
			$next_steps = '
			<form action="/signin/" method="get">
				<p><input type="submit" value="Return to Sign-in Page"></p>
			</form>
			';
			
		
		} else {
		
			$next_steps = '
			<h3>Next Steps</h3>
			
			<ul>
			
			<li><a href="/">Return to my home page</a></li>
			
			<li><a href="/student/add/">Add a new student</a></li>
		
			<li><a href="/member/edit/' . $member_id . '/">Edit this student</a></li>
		
			</ul>
			
			';
		
		}
	
		echo '
		<h1>Sign-up Complete</h1>
		
		<p>Sign-up was successful.</p>

		<p>Please print this page  or write down the following information.</p>
		
		<p><strong>Username:</strong> ' . $_REQUEST['username'] . '</p>

		<p><strong>Password:</strong> ' . $_REQUEST['password'] . '</p>
		
		' . $next_steps . '
		
		';
	
	}

} else {

	$custom_fields = '';

	if ($_PORTAL['section'] == 'signup') {
	
		$custom_fields .= '<p><label for="class-word">Sign-up Word</label> <input type="text" name="class_word" id="class-word" value="" size="35"> <span class="form-field-info">Your teacher will provide you with this word</span></p>';

	} else {

		if ($_SESSION['portal']['member_type'] == 'teacher') {
			$teacher_id = $_SESSION['portal']['member_id'];
		} else {
			$teacher_id = '';
		}

		$custom_fields .= portal_generate_class_select_list($_SESSION['portal']['member_school'], $teacher_id);

		$custom_fields .= '<p><label for="interface">Interface</label> ' . portal_generate_interface_list($_SESSION['portal']['member_school'], $teacher_id) . '</p>';

		$custom_fields .= '<input type="hidden" name="school_id" value="' . $_SESSION['portal']['member_school'] . '">';

	}

	echo '
	<form action="/' . $_PORTAL['section'] . '/' . $_PORTAL['activity'] . '/process/" method="post">
	
	<h1>Student Registration</h1>
	
	<p><label for="first-name">First Name</label> <input type="text" name="first_name" id="first-name" value="" size="35"></p>

	<p><label for="last-name">Last Name</label> <input type="text" name="last_name" id="last-name" value="" size="35"></p>
	
	<p><label for="password">Password</label> <input type="text" name="password" id="password" value="" size="35"> <span class="form-field-info"><strong>Warning:</strong> this field will display your password<br><strong>Note:</strong> your password must be between 4 and 40 characters long</span></p>
	
	' . $custom_fields . '

	<p><label for="submit">&nbsp;</label> <input type="submit" id="submit" value="Continue"></p>
	
	<div class="clear-both">&nbsp;</div>
	
	</form>
	
	';

}
	
?>
