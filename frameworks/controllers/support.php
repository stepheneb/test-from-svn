<?php

$page_title = 'Support';

if ($_PORTAL['activity'] == 'process') {

	// prepare the email
	
	$to = 'portal-support@concord.org';

	$from = 'portal-support@concord.org';
	
	$subject = '[PORTAL] Support Request';

	$cc = array('sfentress@concord.org');

	$bcc = array();

	$reply_to = @$_REQUEST['email'];
	
	$body = '
++++++++++++++++++++++++++++++++++++++++++++++++++++
    P O R T A L   S U P P O R T   R E Q U E S T
++++++++++++++++++++++++++++++++++++++++++++++++++++

From: 
' . @$_REQUEST['name'] . ' <' . @$_REQUEST['email'] . '>

Class:
' . @$_REQUEST['class'] . '

Activity:
' . @$_REQUEST['class'] . '

Number of Students:
' . @$_REQUEST['number_students'] . '

Computer Type:
' . @$_REQUEST['computer_type'] . '

Java WebStart Fix applied?
' . @$_REQUEST['JavaWebStartFix'] . '

Problem Type:
' . @$_REQUEST['Problems'] . '

All computers had problem?
' . @$_REQUEST['SameProblem'] . '

Were computers preloaded?
' . @$_REQUEST['Preloaded'] . '

Additional Details:
' . @$_REQUEST['elaborate'] . '

Time support request sent:
' . date('g:i a \o\n n/j/Y') . '

User web browser:
' . $_SERVER['HTTP_USER_AGENT'] . '

++++++++++++++++++++++++++++++++++++++++++++++++++++
          H A V E   A   N I C E   D A Y ! 
++++++++++++++++++++++++++++++++++++++++++++++++++++

';

	// send the email and show a confirmation page
	
	if (mystery_send_email($to, $from, $subject, $body, '', $cc, $bcc, $reply_to)) {
		
		echo '
		<h2>Support request sent</h2>
		
		<p>Your support request has been sent.  If appropriate, we will contact you shortly.</p>
		';
		
	} else {
	
		echo '
		<h2>Error! Message not sent</h2>
		
		<p>Your message could not be sent.  Please contact us directly via email at <a href="mailto:portal-support@concord.org">portal-support@concord.org</a></p>
		
		';
	
	}

} else {

	$name_value = '';
	$email_value = '';
	
	if (@$_SESSION['is_logged_in'] == 'yes') {
	
		$name_value = $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name'];
	
		if ($_SESSION['portal']['member_type'] != 'student') {
			$email_value = $_SESSION['user_email'];
		}
	
	}

	echo '
	<p><strong>Do you have a technical support problem? You\'ve come to the right place</strong></p>
	
	<p>We encourage you to take a look at our <a href="/help/">help documents</a> before contacting us.</p>
	
	<hr>
	
	<h2>Contact technical support</h2>
	
	
	<form action="/support/process/" method="post">
	
	
	<p>Please complete the following form if you are experiencing problems with activities.</p>
	
	<p>The support team will receive a copy of your information by email. If you need immediate assistance, please call The Concord Consortium at 978-405-3200.
	<!-- Sam Fentress orat 978-405-3238 --></p>
	
	<p><strong>Name</strong><br>
	<input type="text" name="name" id="name" value="' . $name_value . '" size="50"></p>
	
	<p><strong>Email</strong><br>
	<input type="text" name="email" id="email" value="' . $email_value . '" size="50"></p>
	
	<p><strong>Class</strong><br>
	<input type="text" name="class" id="class" size="50">
	<br><small>(Please complete a separate incidence report form for each individual class period.)</small></p>
	
	<p><strong>Unit or activity name</strong><br>
	<input type="text" name="UDL_activity" id="UDL_activity" size="50"></p>
	
	<p><strong>Number of students</strong><br>
	<input type="text" name="number_students" id="number_students" size="10"></p>
	
	<p><strong>Type of computers</strong><br>
	<input type="text" name="computer_type" id="computer_type" size="50"></p>
	
	<p><strong>Did you run the Java WebStart fix (Mac only)?</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="JavaWebStartFix" value="Yes" id="JavaWebStartFix_0"> Yes
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="JavaWebStartFix" value="No" id="JavaWebStartFix_1"> No
	</p>
	
	<p><strong>Are you experiencing:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="Problems" value="Download Problems" id="Problems_0"> Download problems
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="Problems" value="Upload Problem" id="Problems_1"> Uploading (saving) problems
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="Problems" value="Viewing Past Work" id="Problems_2"> Viewing past work
	</p>

	<p><strong>Did all computers experience the same problem?</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="SameProblem" value="Yes" id="SameProblem_0"> Yes
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="SameProblem" value="No" id="SameProblem_1"> No
	</p>

	<p><strong>Were the computers preloaded prior to student use?</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="Preloaded" value="Yes" id="Preloaded_0"> Yes
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="Preloaded" value="No" id="Preloaded_1"> No
	<br><small>(Preloading is running the software on each computer before your students do sow that it works more quickly)</small>
	</p>
	
	<p><strong>Please elaborate:</strong><br>
	<textarea name="elaborate" cols="50" rows="5" wrap="soft" id="elaborate"></textarea></p>

	
	<p><input type="submit" value="Submit"></p>
	
	</form>
	
	
	<p><em>You can also email <a href="mailto:portal-support@concord.org">portal-support@concord.org</a> if you prefer or this form is not working.</em></p>
	
	';


}	
	
?>