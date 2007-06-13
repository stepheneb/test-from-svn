<?php
// Send the appropriate header file
header('Content-Type: text/html; charset=utf-8');

// Set some default variables
$extra_head_element = '';
$extra_body_attribute = '';

// if javascript is enabled, set some varialbes
if (@$_MYSTERY['enable_javascript'] == 'yes') {
	$extra_head_element .= '<script type="text/javascript" language="javascript" src="' . $_MYSTERY['web_location'] . '/mystery_javascript.js"></script>';
	$extra_body_attribute .= ' onload="highlightAccessKeys();"';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="generator" content="Mystery 4, Copyright 2004 Paul Burney">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo $_MYSTERY['web_location']; ?>/images/favicon.ico">
	<link rel="icon" type="image/x-icon" href="<?php echo $_MYSTERY['web_location']; ?>/images/favicon.ico">
	<title><?php echo htmlspecialchars($_MYSTERY['page_title']); ?></title>
	<style type="text/css" media="screen">
		@import "<?php echo $_MYSTERY['web_location'] . '/mystery_style.css'; ?>";
	</style>
	<?php echo $extra_head_element; ?>
</head>
<body bgcolor="#ffffff"<?php echo $extra_body_attribute; ?>>

<?php
// Need to add checking here to see whether or not to enable javascript
?>
