<?php

if ($_SESSION['portal']['member_type'] != 'superuser' && $_SESSION['portal']['member_type'] != 'admin' && $_SESSION['portal']['member_type'] != 'teacher') {

	mystery_redirect('/');
	exit;

}

// FIXME - Add a check here to see if this is the class teacher if the role is a teacher

if (isset($_PORTAL['params']['process'])) {

	portal_delete_class($_PORTAL['action']);
	mystery_redirect('/');
	exit;
	
} else {

	// FIXME - Maybe add in details on the class to be deleted here

	echo '
	<form action="/class/delete/' . $_PORTAL['action'] . '/process/" method="post">
	<h1>Delete a class?</h1>
	
	<p>Are you <strong>absolutely sure</strong> that you want to delete this class?  There is no undo available.</p>
	
	<p><input type="button" value="No" onclick="history.back();"> <input type="submit" value="Yes"></p>
	</form>
	';

}


?>
