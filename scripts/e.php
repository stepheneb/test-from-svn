<?php

if ($_SESSION['portal']['member_type'] != 'superuser') {
	mystery_redirect('/');
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>PHP Evaluation</title>
</head>
<body style="background-color: #ffcc00;">

<h1>PHP Mini Console</h1>

<div style="background-color: #cccccc; border: 2px dashed #000000;">

<?php

$code = @$_REQUEST['code'];

eval(stripslashes($code));

?>

</div>

<form action="e.php" method="post">

<p><textarea name="code" rows="15" cols="100"><?php echo stripslashes(@$_REQUEST['code']); ?></textarea></p>

<p><input type="submit" value="(E)valuate Code" accesskey="e"></p>

</form>

<p>This mini console has the dev frameworks loaded so can connect to the
databases, etc, interactively.</p>

</body>
</html>
