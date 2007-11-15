<?php

// This page will send a password reminder to a teacher/admin if they've forgotten their password

$page_title = 'Password Reminder';

if (@$_REQUEST['email_address'] == '') {

	echo '
	<form action="/reminder/process/" method="post">
	
	<h1>Don\'t know your password?</h1>
	
	<p>If you are a <strong>Student</strong>, please ask your teacher to give you your password.</p>
	
	<p>If you are a <strong>Teacher</strong>, please enter your email address below and click on the <em>Send my password</em> button:</p>
	
	<p><label for="email-address">Email </label><input id="email-address" type="text" name="email_address" value="" size="35"> <input type="submit" value="Send my password"></p>
	
	</form>
	';

} else {

	// search for email address in system
	
	$query = 'SELECT * FROM portal_members WHERE member_email = ?';
	
	$params = array($_REQUEST['email_address']);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {
	
		// if found, send out a password reminder
		
		$subject = 'ITSI Portal Password Reminder';
		
		$from = 'ITSI Portal <webmaster@concord.org>';
		
		$to = $results[0]['member_email'];
		
		$body = 'Dear ' . $results[0]['member_first_name'] . ' ' . $results[0]['member_last_name'] . ':' . "\n";
		$body .= '' . "\n";
		$body .= 'Someone, probably you, requested a password reminder from the ITSI portal. Your current password information is listed below.' . "\n";
		$body .= '' . "\n";
		$body .= 'Server: http://itsi-portal.concord.org/' . "\n";
		$body .= 'Username: ' . $results[0]['member_username'] . "\n";
		$body .= 'Password: ' . $results[0]['member_password_ue'] . "\n";
		$body .= '' . "\n";
		$body .= 'If you have any difficulties, please contact webmaster@concord.org.' . "\n";
	
		if (mystery_send_email($to, $from, $subject, $body)) {
		
			echo '
			<h1>Reminder Sent!</h1>
			
			<p>A password reminder has been sent to your email account.  If you don\'t receive it within a few minutes, please be sure to check your Junk Mail or Spam folder 
			as it may have been misclassified.  If you still don\'t get the message, please send an email to <a href="mailto:webmaster@concord.org">webmaster@concord.org</a>.</p>
			';
		
		} else {
		
			$errors = array();
			
			$errors[] = 'Could not send email message';
		
			echo portal_generate_error_page($errors);
		
		}
	
	} else {
		
		// if not found, show an error message for them to contact the webmaster
	
		$errors = array();
		
		$errors[] = 'Email address not found in the system';
	
		echo portal_generate_error_page($errors);
	
	}

}

?>