<?php

$page_title = 'Switch';

if ($_SESSION['portal']['member_type'] != 'superuser') {

	mystery_redirect('/');
	exit;

}

if ($_PORTAL['activity'] == 'process' && $_REQUEST['school_id'] != $_SESSION['portal']['member_school']) {
	$_SESSION['portal']['member_school'] = $_REQUEST['school_id'];
}

$school_info = portal_get_school_info($_SESSION['portal']['member_school']);

echo '
<h1>Switch your school</h1>

<p>You are currently in the school:</p>

<p><strong>' . $school_info['school_name'] . '</strong><br>
' . $school_info['school_city'] . ', ' . $school_info['school_state'] . ' ' . $school_info['school_zip'] . '
</p>

<form action="/switch/process" method="post">

<p><label for="school-id">Switch to</label> <select name="school_id" id="school-id"><option value="">Please select…</option>' . portal_generate_school_option_list() . '</select> <input type="submit" value="Go"></p>

</form>
';

?>