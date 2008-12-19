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
		
		case 'about':
			$include_file = 'controllers/about.php';
		break;
		
		case 'help':
			$include_file = 'controllers/help.php';
		break;
		
		case 'faq':
			$include_file = 'controllers/faq.php';
		break;
		
		case 'requirements':
			$include_file = 'controllers/requirements.php';
		break;
		
		case 'support':
			$include_file = 'controllers/support.php';
		break;
		
		case 'studenthome':
			$include_file = 'controllers/student-home.php';
		break;

		case 'signin':
			$include_file = 'controllers/signin.php';
		break;
		
		case 'reminder':
			$include_file = 'controllers/reminder.php';
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

		case 'links':
			$include_file = 'controllers/links.php';		
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
		
		case 'otrunk':
			$include_file = 'controllers/otrunk.php';
		break;
		
		case 'course':
			$include_file = 'controllers/course.php';
		break;
		
		case 'activities':
		
			switch($_PORTAL['activity']) {
			
				case 'view':
					$include_file = 'controllers/activity-view.php'; // deprecated
				break;
			
				case 'list':
				case 'my':
				case 'school':
				case 'world':
					$include_file = 'controllers/activity-list.php'; // deprecated
				break;
			
				case 'create':
				default:
					$include_file = 'controllers/activities.php';
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
			
				case 'list':
					$include_file = 'controllers/class-list.php';
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
		
		case 'district':
		
			switch($_PORTAL['activity']) {
			
				case 'edit':
					$include_file = 'controllers/district-edit.php';
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

                case 'statistics':
                        $include_file = 'controllers/usage-statistics.php';
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
