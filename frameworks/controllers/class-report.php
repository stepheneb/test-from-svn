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

<p>We apologize but this feature is not yet available.  It will look something like the image below:</p>

<p><img src="/images/screenshots/class-report.png"></p>

';

?>