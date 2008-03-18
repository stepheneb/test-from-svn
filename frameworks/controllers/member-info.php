<?php

$page_title = 'Info';

if ($_SESSION['portal']['member_type'] != 'student') {
	$email_field = '<strong>Email:</strong> ' . $_SESSION['user_email'] . '<br>';
} else {
	$email_field = '';
}

echo '
<h2>My Info <a href="/member/edit/' . $_SESSION['portal']['member_id'] . '/" class="heading-link">' . portal_icon('setup') . ' Change this information</a></h2>

<p>
<strong>Name:</strong> ' . $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name'] . '<br>
' . $email_field . '
<strong>Username:</strong> ' . $_SESSION['user_username'] . '<br>
<strong>Interface:</strong> ' . $portal_config['interfaces'][$_SESSION['portal']['member_interface']] . '<br>
<strong>Password: </strong> <em>hidden</em>
</p>

';

?>
