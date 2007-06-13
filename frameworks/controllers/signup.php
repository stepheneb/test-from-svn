<?php

$page_title = 'Sign Up';


switch($_PORTAL['activity']) {

	case 'student':
	
		include 'controllers/student-signup.php';
		
	break;
	
	case 'teacher':
	
		include 'controllers/teacher-signup.php';
		
	break;
	
	default:
	
		echo '
		<h1>Please select an option below</h1>
	
		<ul>
		
		<li><a href="/signup/student/">Sign up as a <strong>Student</strong></a></li>
		
		<li><a href="/signup/teacher/">Sign up as a <strong>Teacher</strong></a></li>
		
		</ul>
		';
		
	break;

}

?>
