<?php

$page_title = 'Activities';

if (in_array('create', $GLOBALS['portal_config']['available_actions'])) {
	
	echo '
	<h2>Activity Creation</h2>
	
	<p>Activities used in the ITSI project are created using a system called <strong><a href="/diy/home/" target="_blank">Do-It-Yourself</a></strong>.</p>
	
	<h3>Make your own activity</h3>
	
	<p><a href="/diy/new/" target="_blank">' . portal_icon('add') . '</a> <a href="/diy/new/" target="_blank">Create a new activity</a> in the <strong>Do-It-Yourself</strong> site</p>
	
	';

}

if (in_array('copy', $GLOBALS['portal_config']['available_actions'])) {
	
	echo '
	
	<h3>Copy an existing activity</h3>
	
	<p>Click on the copy icon ' . portal_icon('copy') . ' next to an activity name to make your own customized version of it.</p>
	
	';

}

echo portal_generate_activity_grid(array(), array(), 'full');

?>
