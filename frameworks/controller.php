<?php

// This is the main controller file for the gl website


function portal_determine_resource($uri) {

	// This function determines which resource the user should be given, based on the current

	// section -> activity -> action -> params

	$_PORTAL = portal_parse_uri($uri);

	switch($_PORTAL['section']) {
	
		case '':
		case 'home':

			if ($_SESSION['portal']['member_type'] == 'student') {
				$include_file = 'controllers/student-home.php';
			} else {
				$include_file = 'controllers/home.php';
			}

		break;

		case 'signin':
			$include_file = 'controllers/signin.php';
		break;
		
		case 'signout':
			$include_file = 'controllers/signout.php';
		break;
		
		case 'signup':
			$include_file = 'controllers/signup.php';
		break;
		
		case 'switch':
			$include_file = 'controllers/switch.php';		
		break;

		case 'admin':
			$include_file = 'controllers/admin.php';
		break;
		
		case 'preview':
			$include_file = 'controllers/preview.php';
		break;
		
		case 'diy':
			$include_file = 'controllers/diy.php';
		break;
		
		case 'activity':
		
			switch($_PORTAL['activity']) {
			
				case 'view':
					$include_file = 'controllers/activity-view.php';
				break;
			
				case 'create':
					$include_file = 'controllers/activity-create.php';
				break;
				
				case 'list':
				case 'my':
				case 'school':
				case 'world':
					$include_file = 'controllers/activity-list.php';
				break;
			
			}
			
		break;
		
		
		case 'class':
		
			switch($_PORTAL['activity']) {
			
				case 'edit':
				case 'add':
				case 'copy':
					$include_file = 'controllers/class-manage.php';
				break;
				
				case 'delete':
					$include_file = 'controllers/class-delete.php';
				break;
			
				case 'roster':
					$include_file = 'controllers/class-roster.php';
				break;

				case 'report':
					$include_file = 'controllers/class-report.php';
				break;
			
				case 'preview':
					$include_file = 'controllers/student-preview.php';
				break;
			
			}
			
		break;
		
		
		case 'school':
		
			switch($_PORTAL['activity']) {
			
				case 'edit':
					$include_file = 'controllers/school-edit.php';
				break;
			
			}
			
		break;
		
		case 'student':
		
			switch($_PORTAL['activity']) {
			
				case 'add':
					$include_file = 'controllers/student-signup.php';
				break;
			
			}
			
		break;
		
		case 'teacher':
		
			switch($_PORTAL['activity']) {
			
				case 'add':
					$include_file = 'controllers/teacher-signup.php';
				break;
			
			}
			
		break;
		
		case 'member':
		
			switch($_PORTAL['activity']) {
			
				case 'report':
					$include_file = 'controllers/member-report.php';
				break;
			
				case 'edit':
					$include_file = 'controllers/member-edit.php';
				break;
			
				case 'delete':
					$include_file = 'controllers/member-delete.php';
				break;
			
				case 'info':
					$include_file = 'controllers/member-info.php';
				break;
			
			}
			
		break;
		
		case 'usage':
			$include_file = 'controllers/usage-stats.php';
		break;
		
		
		default:
			$include_file = 'controllers/error.php';
		break;
	
	}
	
	return $include_file;

}


?>