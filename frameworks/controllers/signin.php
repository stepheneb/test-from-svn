<?php

$page_title = 'Welcome';

$login_failed = 'no';
$login_failure_reason = '';
$alert = '';

$lastloc = @$_COOKIE['lastlocation'];

if ($lastloc == '') {
	$lastloc = '/';
}

if (preg_match('~/process/$~', $lastloc)) {

	//show error
	$note = '<strong>An error has occurred:</strong><br>Your session timed out and we were therefore unable to process your previous request/submission. Please resubmit your request after signing in again. Thank you.';
	
	echo '<br>' . portal_generate_notebox($note);

	//remove the trailing /process/
	$lastloc = preg_replace('~/process/$~','/', $lastloc);

}

if (@$_SESSION['is_logged_in'] == 'yes') {
	mystery_redirect('/');
	exit;
}

// attempt a login and redirect

if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {

	if (mystery_auth($_REQUEST['username'], $_REQUEST['password'])) {
		
		if (!isset($_COOKIE['cookietest'])) {
		
			// they know their username and password but since they do not
			// have cookies enabled, they won't be able to use the site

			$login_failed = 'yes';
			$login_failure_reason = 'cookie';
		
		} else {
			
			mystery_redirect($lastloc);
			
		}
		
	} else {
	
		$login_failed = 'yes';

	}
	
}

// destroy any existing sessions

mystery_setup_default_session();

// display alert messages if necessary

if ($login_failed == 'yes') {

	if ($login_failure_reason == 'cookie') {
	
		$alert = '<p class="error-message"><strong>There was a problem signing in.</strong><br>
		It appears that you do not have cookies enabled.  You must enable cookies
		in order to use this site.</p>';
	
	} else {

		$alert = '<p class="error-message"><strong>There was a problem signing in.</strong><br>
		Please check your username and password again. If you do not know your
		password, contact your teacher or school administrator.</p>';
	
	}

}

if (isset($_REQUEST['signout'])) {

	$alert = '
	<p class="alert-message"><strong>Sign-out successful</strong><br>
	You may sign in again below</p>
	';

}

mystery_cookie('cookietest','yes');

// display the sign-in and registration links

echo '
<table width="100%">
<tr>
<td valign="top" width="50%">

<div class="left-column-container">
<div class="left-column-content">

	<h2>Already Signed Up?</h2>
	
	' . $alert . '
	
	<form action="/signin/process/" method="post">
	
	<p><label for="username">Username</label> <input type="text" name="username" id="username" value="" size="20"></p>
	
	<p><label for="password">Password</label> <input type="password" name="password" id="password" value="" size="20"></p>
	
	<p><label for="submit">&nbsp;</label> <input type="submit" id="submit" value="Sign In"></p>
	
	<div class="clear-both">&nbsp;</div>

	<p><label for="password-link">&nbsp;</label><a id="password-link" href="/reminder/"><strong>I don\'t know my password!</strong></a></p>

	</form>

	<h2>First Time Here?</h2>
	
	<ul>
	
	<li><a href="/signup/student/">Sign up as a <strong>Student</strong></a></li>
	
	<li><a href="/signup/teacher/">Sign up as a <strong>Teacher</strong></a></li>
	
	</ul>
	
	<h2>Just Want to Try the Units?</h2>
	
	<ul>
	
	<li><a href="/preview/">View our unit previews</a></li>
	
	</ul>


</div>
</div>

</td><td valign="top" width="50%">

<div class="right-column-container">
<div class="right-column-content">

';

if (@$portal_config['show_front_page_image'] != 'no') {

	echo '<img src="/images/students-using-portal.jpg">';

}

echo '
</div>
</div>

</td>
</tr>

</table>

' . portal_generate_technical_notes_section() . '


<div class="clear-both">&nbsp;</div>

';

?>
